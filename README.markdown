#Cotizador V 0.1
### <font color="red">¡Se agrego la carpeta vendor a git por que el cliente usa un webshare!</font> 

## Cambios de la version
- Se agrego silex como base para desarrollo. 
- Twig maneja las plantillas.
- Los selects condicionales se llenaran via ajax.

Cotizador anterior: http://consejeriaestudiosenelexterior.com/bk-cb/cotizacion/cotizador-cb-2015.php

## Avance. 
- Se agrego forms de symfony2 para manejar los selects
- Se agregaron los dato iniciales al select curso.
- Hidratar pais
- Hidratar ciudad
- Hidratar centro
- Hidratar semanas

<b>Ejecutar en la consola o termina (solo modo desarrollo): php -S localhost:8080 -t web web/index.php</b>