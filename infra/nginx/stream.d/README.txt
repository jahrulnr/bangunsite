# reference: https://docs.nginx.com/nginx/admin-guide/load-balancer/tcp-udp-load-balancer/

stream {

    server {
        listen     12345;
        #TCP traffic will be forwarded to the "stream_backend" upstream group
        proxy_pass stream_backend;
    }

    server {
        listen     12346;
        #TCP traffic will be forwarded to the specified server
        proxy_pass backend.example.com:12346;
    }

    server {
        listen     53 udp;
        #UDP traffic will be forwarded to the "dns_servers" upstream group
        proxy_pass dns_servers;
    }
    # ...
}
