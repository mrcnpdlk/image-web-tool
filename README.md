# Image web tool

Simple image web tool manipulation package - other than all.

## Powers


## Configuration


https://www.behance.net/gallery/33043073/BlowBrush-free-font

### Request url schema
Example
```code
http://example.com/iwt/v1/{params}/{fileName}
```

`params` and `fileName` are pass to the api.php file.

### Request Options (`params`)

| Option 	| Description  	| Values  	| Notes  	|
|:--:	|---	|---	|---	|
| w   	| width  	| value in px  	|   	|
| h   	| height  	| value in px  	|   	|
| c   	| crop mode  	| enum: scale,fit,fit-margin,fill  	|   	|
| q   	| quality  	| 0-100  	| only JPG format supported  	|
| r   	| rotate  	| angle in degrees  	|   	|
| bgc   	| background color  	| HEX format  	|   	|
| e   	| effect  	|   enum: g (gamma), n (negative), gr (grayscale), c (colorize), b (blur)|  	|
| eo   	| effect option  	| string,int - depends on effect type, see table below  	|   	|

| Effect [e] 	| Description  	| Option [eo]  	| Default  	|
|:--:	|---	|---	|---	|
|g|gamma|gamma correcion|1|
|n|negative|||
|gr|grayscale|||
|c|colorize|color in HEX format|#FFFFFF|
|b|blur|sigma|1|

### Nginx Configuration



```
server {
    listen 127.0.0.1:9081 default_server;

    root API_DIR_LOCATION;

    index api.php;

    server_name _;

    location / {
        try_files $uri $uri/ /api.php?$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        }
}
```

```
server {
    ## LISTEN
    listen 81;

    ## SERVER
    server_name example.com;

    root ROOT_DIR;
    index index.php index.html index.htm index.nginx-debian.html;

    location /iwt/v1 {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header X-NginX-Proxy true;

        rewrite ^/iwt/v1/?(.*) /$1 break;

        access_log /var/log/nginx/iwt-access.log;
        error_log /var/log/nginx/iwt-error.log;

        proxy_pass http://127.0.0.1:9081;
        proxy_redirect off;
    }
```
