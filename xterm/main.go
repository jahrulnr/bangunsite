package main

import (
	"crypto/tls"
	"log"
	"net/http"
	"os"

	"github.com/jahrulnr/go-ssh-web-client/core"
)

func main() {
	handler := &core.SshHandler{}

	port := "13999"
	dev := os.Getenv("dev")
	if dev == "true" {
		port = "9000"
	}
	server := &http.Server{
		Addr:    ":" + port,
		Handler: nil,
		TLSConfig: &tls.Config{
			MinVersion: tls.VersionTLS12,
		},
	}
	http.HandleFunc("/ssh/connection", handler.WebSocket)
	sslPath := "/storage/webconfig/ssl/live/default/"
	if _, err := os.Stat(sslPath); err != nil {
		http.Handle("/", http.FileServer(http.Dir("./front/")))
		sslPath = "../config/webconfig/ssl/live/default/"
	}
	log.Fatal(server.ListenAndServeTLS(sslPath+"cert.pem", sslPath+"key.pem"))
}
