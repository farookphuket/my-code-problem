# my-code-problem on test


## this is the fail code repo 
> just to make a question and share the code problem 

6 aug 2022 laravel sanctum return 401 to nextjs and postman 
> the code propblem has been solved on `8 aug 2022` to get the working repo please visit [**here**](https://github.com/farookphuket/laravel-nextjs-login)


[success_status]:https://archive.org/download/arch_linux_myconfig_24-aug-2022_edit/success_STATUS.png

[fail_status]:https://archive.org/download/arch_linux_myconfig_24-aug-2022_edit/fail_STATUS.png

# STATUS 
> this code problem has been solved

![success_status]
> the poblem has been solved on 8 aug 2022

# last update 6 Aug 2022 

> at 6-aug-2022 09:30 am still no luck 
> still got 401 error when try to get /api/user from App\Http\AuthController 
> this seem to be long problem for me 3 days alredy!


## I still cannot get the use authentication

> even the use has login but still get "401" error I really have no idea now 
> so that's why I made this repo


## to test this 

if you just clone this to your computer this code cannot be run as you know 
    that i have remove the folder "vendor","node_modules" just to make the 
    file smaller to github so if you do the following maybe help.

1. rename file ".env.EXAMPLE" to ".env" 

2. edit the ".env" file 

```

DB_CONNECTION=sqlite
DB_DATABASE=/home/your-name/your-project-folder/DB/DB.sqlite

# either of this not work for me
#SESSION_DOMAIN=l9.next
#SESSION_DRIVER=file
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# in my case I have this project on vhost at the path /srv/http/l9.next
SESSION_DOMAIN=.l9.next
SANCTUM_STATEFUL_DOMAINS=l9.next
```

then run `composer update` to get the folder "vendor"

3. rename "clientX" to "client"

4. rename "client/.env.local.EXAMPLE" to "client/.env.local" 

5. i have remove the folder "node_modules" from "client" 
just to make the file smaller so to get it back run 

```
    cd client
    yarn install && yarn dev
```


remember all the help from you is big big thanks from me.





