#Cotizador V 0.1
### <font color="red">¡Se agrego la carpeta vendor a git por que el cliente usa un webshare!</font> 

## Cambios de la version
- Se agrego silex como base para desarrollo. 
- Twig maneja las plantillas.
- Los selects condicionales se llenaran via ajax.

Cotizador anterior: http://consejeriaestudiosenelexterior.com/bk-cb/cotizacion/cotizador-cb-2015.php

## En producción:
- Todos los cambios realizados.

## PRUEBAS DE CLIENTE
1. SEGURO OBLIGATORIO: en el badge donde dice ASISTENCIA debe ir el valor que esta en la tabla v_seguro de acuerdoal pais y numero de semanas.
2. En algunos países como Inglaterra no aparecen las opciones ordenadas de las semanas del curso.
3. Hay que hacer más pequeño el formulario en vista de computador.
4. ESTADIA: parece que sigue el problema con la acumulacion de precios entre tipo de alojamiento, habitacion y tipo de alimentación me dicen que hay varios que no corresponden al valor y no toman el valor de acuerdo a las semanas de alojamiento.

<b>Ejecutar en la consola o termina (solo modo desarrollo): php -S localhost:8080 -t web web/index.php</b>

###Configuración del werb server
- http://silex.sensiolabs.org/doc/web_servers.html