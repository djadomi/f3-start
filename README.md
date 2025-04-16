# f3-start

Just a skeleton f3 site.

Get a copy of this and start a new project with:

``` bash
git clone --depth=1 git@github.com:djadomi/f3-start.git NEWPROJECTNAME
cd NEWPROJECTNAME
rm -rf ./.git
composer update
git init
mkdir logs tmp
sudo chown www-data logs tmp
# optionally create a placeholder for favicon to prevent 404 errors:
touch htdocs/favicon.ico
```
