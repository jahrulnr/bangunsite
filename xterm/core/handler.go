package core

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"time"

	"github.com/gorilla/websocket"
	"golang.org/x/crypto/ssh"
)

const (
	// Time allowed to write or read a message.
	messageWait = 10 * time.Second

	// Maximum message size allowed from peer.
	maxMessageSize = 512
)

var terminalModes = ssh.TerminalModes{
	ssh.ECHO:          1,     // enable echoing (different from the example in docs)
	ssh.TTY_OP_ISPEED: 14400, // input speed = 14.4kbaud
	ssh.TTY_OP_OSPEED: 14400, // output speed = 14.4kbaud
}

var upgrader = websocket.Upgrader{
	ReadBufferSize:  maxMessageSize,
	WriteBufferSize: maxMessageSize,
	CheckOrigin: func(r *http.Request) bool {
		return true
	},
	HandshakeTimeout:  time.Duration(10) * time.Second,
	EnableCompression: true,
}

type sshConfig struct {
	Host string `json:"host"`
	Port string `json:"port"`
	User string `json:"user"`
	Pass string `json:"pass"`
}

type requestPayload struct {
	High  int `json:"high"`
	Width int `json:"width"`
}

type SshClient struct {
	conn     *websocket.Conn
	request  *http.Request
	client   *ssh.Client
	sess     *ssh.Session
	sessIn   io.WriteCloser
	sessOut  io.Reader
	closeSig chan struct{}
}

func (c *SshClient) config() (*sshConfig, *requestPayload, error) {
	c.conn.SetReadDeadline(time.Now().Add(messageWait))
	msgType, msg, err := c.conn.ReadMessage()
	if msgType != websocket.BinaryMessage {
		err = fmt.Errorf("conn.ReadMessage: message type is not binary")
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		return nil, nil, err
	}
	if err != nil {
		err = fmt.Errorf("conn.ReadMessage: %w", err)
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		return nil, nil, err
	}

	payload := new(requestPayload)
	if err := json.Unmarshal(msg, payload); err != nil {
		err = fmt.Errorf("json.Unmarshal: %w", err)
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		return nil, nil, fmt.Errorf("bridgeWSAndSSH: %v", err)
	}

	dev := os.Getenv("dev")
	config := &sshConfig{}
	if query := c.request.URL.Query(); query.Get("key") != "sample" {
		client := &http.Client{
			Timeout: time.Duration(15) * time.Second,
		}

		requestBody, _ := json.Marshal(&struct {
			Client string
		}{
			Client: "GoSSHClient",
		})
		request, err := http.NewRequest(http.MethodPost, "http://127.0.0.1:8000/api/validateSSH?key="+query.Get("key"), bytes.NewReader(requestBody))
		if err != nil {
			return nil, nil, err
		}

		response, err := client.Do(request)
		if err != nil {
			return nil, nil, err
		}

		defer response.Body.Close()
		body, err := io.ReadAll(response.Body)
		if err != nil {
			return nil, nil, err
		}

		err = json.Unmarshal(body, &config)
		if err != nil {
			log.Panicln(string(body))
			return nil, nil, err
		}
	} else if dev == "true" {
		config.Host = "localhost"
		config.Port = "22"
		config.User = "jahrul"
		config.Pass = "bismillah"
	}
	return config, payload, nil
}

func (c *SshClient) wsWrite() error {
	defer func() {
		c.closeSig <- struct{}{}
	}()

	data := make([]byte, maxMessageSize)

	for {
		time.Sleep(10 * time.Millisecond)
		n, readErr := c.sessOut.Read(data)
		if n > 0 {
			c.conn.SetWriteDeadline(time.Now().Add(messageWait))
			if err := c.conn.WriteMessage(websocket.TextMessage, data[:n]); err != nil {
				return fmt.Errorf("conn.WriteMessage: %w", err)
			}
		}
		if readErr != nil {
			return fmt.Errorf("sessOut.Read: %w", readErr)
		}
	}
}

func (c *SshClient) wsRead() error {
	defer func() {
		c.closeSig <- struct{}{}
	}()

	var zeroTime time.Time
	c.conn.SetReadDeadline(zeroTime)

	for {
		msgType, connReader, err := c.conn.NextReader()
		if err != nil {
			return fmt.Errorf("conn.NextReader: %w", err)
		}
		if msgType != websocket.BinaryMessage {
			if _, err := io.Copy(c.sessIn, connReader); err != nil {
				return fmt.Errorf("io.Copy: %w", err)
			}
			continue
		}

		data := make([]byte, maxMessageSize)
		n, err := connReader.Read(data)
		if err != nil {
			return fmt.Errorf("connReader.Read: %w", err)
		}

		// log.Println("data:", string(data))

		var wdSize requestPayload
		if err := json.Unmarshal(data[:n], &wdSize); err != nil {
			return fmt.Errorf("json.Unmarshal: %w", err)
		}

		// log.Println("wdSize:", wdSize)

		if err := c.sess.WindowChange(wdSize.High, wdSize.Width); err != nil {
			return fmt.Errorf("sess.WindowChange: %w", err)
		}
	}
}

func (c *SshClient) BridgeWSAndSSH() {
	defer c.conn.Close()

	sshConfig, payload, err := c.config()
	if err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Panicln(err)
		return
	}

	config := &ssh.ClientConfig{
		User: sshConfig.User,
		Auth: []ssh.AuthMethod{
			ssh.Password(sshConfig.Pass),
		},
		// InsecureIgnoreHostKey returns a function
		// that can be used for ClientConfig.HostKeyCallback
		// to accept any host key.
		// It should not be used for production code.
		HostKeyCallback: ssh.InsecureIgnoreHostKey(),
	}
	addr := fmt.Sprintf("%s:%v", sshConfig.Host, sshConfig.Port)
	c.client, err = ssh.Dial("tcp", addr, config)
	if err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Println("bridgeWSAndSSH: ssh.Dial:", err)
		return
	}
	defer c.client.Close()

	c.sess, err = c.client.NewSession()
	if err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Println("bridgeWSAndSSH: client.NewSession:", err)
		return
	}
	defer c.sess.Close()

	c.sess.Stderr = os.Stderr // TODO: check proper Stderr output
	c.sessOut, err = c.sess.StdoutPipe()
	if err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Println("bridgeWSAndSSH: session.StdoutPipe:", err)
		return
	}

	c.sessIn, err = c.sess.StdinPipe()
	if err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Println("bridgeWSAndSSH: session.StdinPipe:", err)
		return
	}
	defer c.sessIn.Close()

	if err := c.sess.RequestPty("xterm", payload.High, payload.Width, terminalModes); err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Println("bridgeWSAndSSH: session.RequestPty:", err)
		return
	}
	if err := c.sess.Shell(); err != nil {
		c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
		log.Println("bridgeWSAndSSH: session.Shell:", err)
		return
	}

	log.Println("started a login shell on the remote host")
	defer log.Println("closed a login shell on the remote host")

	go func() {
		if err := c.wsRead(); err != nil {
			c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
			log.Println("bridgeWSAndSSH: wsRead:", err)
		}
	}()

	go func() {
		if err := c.wsWrite(); err != nil {
			c.conn.WriteMessage(websocket.TextMessage, []byte(err.Error()))
			log.Println("bridgeWSAndSSH: wsWrite:", err)
		}
	}()

	<-c.closeSig
}

type SshHandler struct {
	Addr    string
	User    string
	Secret  string
	Keyfile string
}

// webSocket handles WebSocket requests for SSH from the clients.
func (h *SshHandler) WebSocket(w http.ResponseWriter, req *http.Request) {
	conn, err := upgrader.Upgrade(w, req, nil)
	if err != nil {
		log.Println("upgrader.Upgrade:", err)
		return
	}

	SshCli := &SshClient{
		conn:     conn,
		closeSig: make(chan struct{}, 1),
		request:  req,
	}
	go SshCli.BridgeWSAndSSH()
}
