# cotizador
primera version

Cotizador en línea

Consta de la actualización de este cotizador que funciona hasta cierto punto:

http://consejeriaestudiosenelexterior.com/bk-cb/cotizacion/cotizador-cb-2015.php

Está cmpuesto de 3 partes, unos datos básicos (parte izquierda), opciones extra (parte central y salen después de poner
los datos básicos) y la parte de precios (parte derecha, debe salir después de los datos básicos y debe rellenar con 
precios lo escogido).

En el cotizador original hay fallos en la parte central que no da datos y no toma los precios o acumula, y también
al cambiar datos al lado izquierdo se queda pegado el ajax y no responde.

FUncionamiento

Va así como estan puestos los selects, se escoge un curso, lanza el país y en la parte derecha debera salir
 el tipo de moneda que ese curso, las ciudades del país,
después los centros educativos de las ciudad, las semanas del curso también definidas y lecciones por semana y la jornada 
escogida por el cliente.

Con estos datos ya deben haber precios en la parte derecha y las opciones del lado central funciona identico de la izquierda
donde alojamiento despliga las siguientes opciones de semana  de alojamiento, tipo alojamiento, tipo de habitacion y tipo de alimentacion
obvimanete si se toma el si dependiendo de lo que se tome va acumulando al lado derecho, igual traslado.
La parte de seguro sale y no va el nuevo cotizador.

Despues de este proceso, al dar en el botón imprimir debe arrojar la opción de imprimir no por pdf si no por navegador
con un formato definido.

Si gustas puedes hacer solo una cotizacion en el antiguo para que te fijes como funciona.

elegi bootstrap y PHP porque tengo solo una semana para hacerlo y tambien tengo el codigo de ese cotizador pero
siguiendo las reocmendaciones de los cursos hay muchas cosas que no deberian ir como Switch y case en los js y try and catch,
pero supongo que abra formas de obtener datos con json en PHP y tratarlos.

Gracias por mirar, en la tarde de hoy te busco por face para ver tus comentarios.
