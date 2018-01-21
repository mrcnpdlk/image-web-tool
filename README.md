# Image web tool

Simple image web tool manipulation package - other than all.

## Instalation

The best way is cloning the repo with specific branch name.
Stable releases are sown [here](https://github.com/mrcnpdlk/image-web-tool/releases).

```bash
git clone -b {STABLE_VERSION} https://github.com/mrcnpdlk/image-web-tool.git
```

## Configuration

In folder config copy file `config.json.dist` and rename to `config.json`.
Edit file and set or delete options:
  - `storage` - path to the folder with pictures
  - `font` - path to the ttf file with font. Used as font on placeholders files.
  - `debug` - if TRUE no placeholder, but exception stack is shown.

Default font [BlowBrush](https://www.behance.net/gallery/33043073/BlowBrush-free-font) is included to the project.


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


### Examples
#### `/h_100/demo.jpg`
Height is set. Default FIT crop mode is enabled.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_100-demo.jpg?raw=true)

#### `/h_200,e_b,eo_5/demo.jpg`
Blur effect.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,e_b,eo_5-demo.jpg?raw=true)

#### `/h_200,e_b,eo_5/demo.jpg`
Colorization effect with pink color.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,e_c,eo_ff4080-demo.jpg?raw=true)

#### `/h_200,e_n/demo.jpg`
Negative effect.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,e_n-demo.jpg?raw=true)

#### `/h_200,q_2/demo.jpg`
Less quality.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,q_2-demo.jpg?raw=true)

#### `/h_200,w_200,c_fill/demo.jpg`
FILL mode.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,w_200,c_fill-demo.jpg?raw=true)

#### `/h_200,w_200,c_fit/demo.jpg`
FIT mode.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,w_200,c_fit-demo.jpg?raw=true)

#### `/h_200,w_200,c_fit-margin/demo.jpg`
FIT MARGIN mode.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,w_200,c_fit-margin-demo.jpg?raw=true)

#### `/h_200,w_200,r_20/demo.jpg`
ROTATE mode.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/h_200,w_200,r_20-demo.jpg?raw=true)

#### `/w_200,h_100/demo.jpg`
File not found. Placeholder is shown.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/w_200,h_100-notfound.png?raw=true)

#### `/w_200,h_100/demo.jpg`
Placeholder is shown.

![alt text](https://github.com/mrcnpdlk/image-web-tool/blob/master/demo/w_200,h_100-placeholder.png?raw=true)
