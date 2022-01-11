## MaintenanceSystem
<a href="https://poggit.pmmp.io/p/MaintenanceSystem"><img src="https://poggit.pmmp.io/shield.state/MaintenanceSystem"></a>
## Version: 0.0.1

## api: pocketmine 4.0

<p>this plugin is a simple plugin to perform maintenance on its server.
it allows via a form to define a time, and thanks to a configuration, to allow to display a desired interval an announcement </p>

##config:
```yaml
maintenance-announce: 'Une maintenance va débuter dans: {time}'
  secound-announce:
  - 30
  - 20
  - 10
  - 5
  tranfere-to-other-server: true
  ip-server: 127.0.0.1
  port-server: "19132"
  message-to-kick: you have bean kicked for Maintenance server
  form-reason: raison
```

<p>other additions will come, do not hesitate to send me suggestions </p>
