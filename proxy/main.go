package main

import (
	"crypto/tls"
	"log"
	"net/http"
	"net/http/httputil"
	"net/url"
	"os"
	"time"
)

func main() {
	remote, err := url.Parse("http://localhost:8000")
	if err != nil {
		panic(err)
	}

	handler := func(p *httputil.ReverseProxy) func(http.ResponseWriter, *http.Request) {
		return func(w http.ResponseWriter, r *http.Request) {
			log.Println(r.URL)
			w.Header().Set("X-Site", "Go")
			r.Header.Add("x-https", "true")
			p.ServeHTTP(w, r)
		}
	}

	proxy := httputil.NewSingleHostReverseProxy(remote)
	http.HandleFunc("/", handler(proxy))
	server := &http.Server{
		Addr:        ":8080",
		Handler:     nil,
		ReadTimeout: time.Duration(60) * time.Second,
		TLSConfig: &tls.Config{
			MinVersion: tls.VersionTLS12,
		},
	}

	sslPath := "/storage/webconfig/ssl/live/default/"
	if _, err := os.Stat(sslPath); err != nil {
		sslPath = "../config/webconfig/ssl/live/default/"
	}

	err = server.ListenAndServeTLS(sslPath+"cert.pem", sslPath+"key.pem")
	if err != nil {
		panic(err)
	}
}
