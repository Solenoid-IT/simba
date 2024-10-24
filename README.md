# Simba
Simba is a complete solution for building professional web-apps.
<br>
It uses <a href="https://github.com/Solenoid-IT/php-core-lib" target="_blank"><b>php-core</b></a> for backend, <a href="https://kit.svelte.dev" target="_blank"><b>sveltekit</b></a> for frontend and <a href="https://capacitorjs.com" target="_blank"><b>capacitor</b></a> for building the mobile-app with the same codebase.
<br>
This app is an <b>SPA</b> (single-page application) with a multi-tenant users system.
<p align="center">
  <img alt="" src="https://dev.simba.solenoid.it/assets/images/simba.png">
</p>
<br><br><br>



# System Requirements
This software is designed for <a href="https://releases.ubuntu.com/22.04/ubuntu-22.04.4-live-server-amd64.iso" target="_blank"><b>Ubuntu Server 22.04</b></a>
<br><br><br>



# CLI
You can execute a specific task from bootstrap.php file (<b>CLI</b> context)
<br>

Syntax: `php bootstrap.php { task-id } { method } ...{ args }`

<br>

Example: `php private/bootstrap.php OnDemand/Test run John Doe`
<br><br><br>



# Scheduler
You can schedule your tasks ( <b>./tasks/scheduler.json</b> )
<br>
Scheduler is managed by the <b>daemon</b>
<br><br><br>



# Daemon
You can use or extend the integrated daemon
<br><br>

Setup :
1. Creating the service -> `sudo php x daemon register { name }` ( default-name is <b>< app-id >.simba</b> )<br>
2. Allowing run at boot -> `sudo simba service enable { name }`
<br><br>

Start: `sudo service { name } start`
<br><br>

Stop: `sudo service { name } stop`
<br><br>

Restart: `sudo service { name } restart`
<br><br><br>



# Setup
1.  Installing spm          -> `bash <(wget -qO- "https://install.solenoid.it/pkgs/spm/1.0.0/setup")`<br>
2.  Installing simba        -> `spm install simba`<br>
3.  Creating a new app      -> `simba app create <fqdn-value> -p <path> -v <version>`<br>
4.  Configuring the file    -> `<app-dir>/app.json`<br>
5.  Configuring the files   -> `<app-dir>/credentials/*`<br>
6.  Moving to the directory -> `cd <app-dir>`<br>
7.  Configuring databases   -> `php x mysql build`<br>
8.  Importing the DB models -> `php x mysql import-models`<br>
9.  Creating the user       -> `php x task OnDemand/User create <tenant> <user> <email>`
10. Building the app        -> `php x build`<br>
<br><br><br>



# Development
To start the dev-server you have to digit :
<br>
`php x dev`
<br><br>
Access to `https://front-dev.{ app-id }`
<br><br>
You can set the fqdn resolution via dns-server or your local system hosts file (ex. <b>/etc/hosts</b> for linux) adding this entry :
<br>
Localhost-Entry = `127.0.0.1 front-dev.{ app-id }`
<br><br>
If you are using <b>VS Code</b> for coding you should open the port <b>5173</b> to localhost
<br><br><br>



# Build
To build the app (web + mobile) you have to digit :
<br>
`php x build`
<br><br><br>



# Release
You can define your release logic inside a file ( ./release.php )
<br><br>
To release the app you have to digit :
<br>
`php x release`
<br><br><br>



# Mode
You can develop your app component (store, service, model, task or controller) in two different modes :
<br><br>
<b>Single</b> Mode -> The component is available under one specific context (<b>http</b> or <b>cli</b>) -> Useful for specific implementations
<br>
<b>Multi</b> Mode -> The component is available for both of the contexts (<b>http</b> and <b>cli</b>) -> Useful for one-time coding