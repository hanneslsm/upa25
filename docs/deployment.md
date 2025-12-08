<!---
 * @package hxi25
 * @version 0.1.0
-->


## FTP Deployment
* Uses a github action: https://github.com/SamKirkland/FTP-Deploy-Action/
* Thanks to https://alphaparticle.com/blog/automating-ftp-deployments-with-github-actions/


### Setup
* Create a FTP user with the path to theme
* create github Action Secrets `https://github.com/USER/REPO/settings/secrets/actions`
* Create `PROD_FTP_HOST`, `PROD_FTP_PASSWORD`, `PROD_FTP_USER`
* For `PROD_FTP_HOST` just enter the server address without anything else, e.g. `svr-01.example.cloud`
* Exclude files in `.github/workflows/poduction.yml`
* PROD will be deployed when a new version is released on the Github Repo


* Same for `STAGING_FTP_HOST`, `STAGING_FTP_PASSWORD`, `STAGING_FTP_USER`
* Staging will be deployed on every push to `main` branch


## Version Release
* Draft a new release in https://github.com/USER/REPO/releases 
* Create a tag, and prefix them with a `v` e.g. `v0.1.0`
* Release title e.g. `0.1.0 - First release`
* Add info
