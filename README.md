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
You can execute a specific task (<b>CLI</b> context)
<br>

Syntax: `php x task {id} {method} ...{args}`

<br>

Example: `php x task OnDemand/Test print`
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
1. Creating the service -> `sudo php x daemon register {name}` ( default-name is <b>{app-id}.simba</b> )<br>
2. Allowing run at boot -> `sudo simba service enable {name}`
<br><br>

Start: `sudo service {name} start`
<br><br>

Stop: `sudo service {name} stop`
<br><br>

Restart: `sudo service {name} restart`
<br><br><br>



# Setup
1.  Installing spm          -> `bash <(wget -qO- "https://install.solenoid.it/spm@1.0.0/setup")`<br>
2.  Installing simba        -> `spm install simba`<br>
3.  Creating a new app      -> `simba app create {fqdn} -p {path} -v {version}`<br>
4.  Moving to the directory -> `cd {project-directory}`<br>
5.  Creating the cert       -> `simba vh make-cert {fqdn} -p {path}` <b>*</b><br>
6.  Initializing the app    -> `php x init`<br>
7.  Configuring the file    -> `{project-directory}/app.json`<br>
8.  Configuring the files   -> `{project-directory}/credentials/*`<br>
9.  Building the sql        -> `php x mysql build:make`<br>
10. Running the built sql   -> `php x mysql build:run`<br>
11. Importing the DB models -> `php x mysql extract-models`<br>
12. Building the app (SPA)  -> `php x build`<br>
13. Creating the user       -> `php x task OnDemand/User create {tenant} {user} {email}`
<br><br>
Example -> `php x task OnDemand/User create "simba" "admin" "email@sample.com"`
<br><br>
<b>*</b> Server with private IP :<br>
1. Enabling "Self-Signed cert" -> `simba vh config {fqdn}`
2. Restarting the web-server   -> `sudo service apache2 restart`
<br><br>
<b>Note</b>
<br>

Simba App is composed by separated fqdn for <b>frontend</b> and <b>backend</b>
<br>

Frontend-URL (for SPA realtime development) = `https://front-dev.{app-id}:5173`
<br>

Backend-URL (for APIs or server-side contents) = `https://{app-id}`

<br><br>
To reach these endpoints from your computer you have to set your local system hosts file (ex. <b>/etc/hosts</b> for linux) adding these two entries :
<br><br>
127.0.0.1 front-dev.{app-id}
<br>
{your-simba-server-ip} {app-id}



# Development
To start the dev-server for a frontend development session you have to digit :
<br>
`php x dev`
<br><br>
Access to `https://front-dev.{app-id}:5173`
<br><br>
If you are using <b>VS Code</b> for coding you should open the port <b>5173</b> to localhost ( <b>localhost:5173</b> )
<br>
Now you can access the <b>Frontend-URL</b> from your computer
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