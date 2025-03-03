# urlshortner
a simple url shortner built with php
---------

###### Functions
- user can choose between either 4 or 5 digits long path
- you can see url click count
- acces code is strictly required to generate shorten-url
- support multiple acces code
<br>**IF** you need more functions, you are welcome to pull request or contact me on [telegram](https://t.me/kyrofur) or email me at [david@kr.md](emailto:david@kr.md)

###### Environments
 - `php 8.1`
 - `nginx 1.21.4`
<br>but im pretty sure you can use any version of these

###### Installation

1. git clone this to your hosting provider or whatever production web environment you have - nginx is great
2. public this site with php
3. add following rule to nginx conf

```nginx
location / {
        try_files $uri /index.php?$query_string;
}

ocation ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.0-fpm.sock; # Update PHP version as needed
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}

location = /database.txt {
      deny all;
      return 403;
}
```

4. replace `httpf://fur.hk` in index.php **line 60** and dash.php **line 22** with yours
5. set acces code in `database.txt` and **DO NOT DELETE BACKSPACE LINE** or php won't read database properly
6. caboom now you have a simple url shortner
