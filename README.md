endpoint post /login
    -modifiqué la forma en la que se reciben los datos desde el servidor porque con el método anterior (getParsedBody) los datos no se interpretaban correctamente y se recibían como null
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -agregué en la respuesta de éxito el valor de 'es_admin' para poder evaluarlo en el código del frontend
    -utilicé el operador null coalescing operator (??) al momento de asignarle un valor a las variables con respecto a lo recibido en la solicitud http. El operador retorna lo que tiene a la izquierda si existe y no es null, sino retorna lo que tiene a la derecha


endpoint post /register
    -modifiqué la forma en la que se reciben los datos desde el servidor porque con el método anterior (getParsedBody) los datos no se interpretaban correctamente y se recibían como null
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -utilicé el operador null coalescing operator (??) al momento de asignarle un valor a las variables con respecto a lo recibido en la solicitud http. El operador retorna lo que tiene a la izquierda si existe y no es null, sino retorna lo que tiene a la derecha


endpoint get /juegos
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -modifiqué la obtención de los datos desde el servidor con respecto a la plataforma para que se pueda enviar más de un valor, luego estos datos son verificados
    -utilicé el operador null coalescing operator (??) al momento de asignarle un valor a las variables con respecto a lo recibido en la solicitud http. El operador retorna lo que tiene a la izquierda si existe y no es null, sino retorna lo que tiene a la derecha
    -ahora además de devolver los juegos que coinciden con los parámetros devuelve la cantidad de paginas (de 5 juegos cada una) que está devolviendo para poder deshabilitar el botón de "siguiente" en el frontend 


endpoint get /juegos/{id}
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -antes devolvía nombre, descripción y solo las calificaciones del juego solicitado pero ahora devuelve nombre, descripción, clasificación de edad, la imágen y sus calificaciones junto con el nombre del usuario que realizó esa calificación


endpoint post /juego
    -modifiqué la forma en la que se reciben los datos desde el servidor porque con el método anterior (getParsedBody) los datos no se interpretaban correctamente y se recibían como null
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -utilicé el operador null coalescing operator (??) al momento de asignarle un valor a las variables con respecto a lo recibido en la solicitud http. El operador retorna lo que tiene a la izquierda si existe y no es null, sino retorna lo que tiene a la derecha
    -modifiqué la obtención de los datos desde el servidor con respecto a la plataforma para que se pueda enviar más de un valor, luego estos datos son verificados


endpoint post /calificacion
    -modifiqué la forma en la que se reciben los datos desde el servidor porque con el método anterior (getParsedBody) los datos no se interpretaban correctamente y se recibían como null
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -utilicé el operador null coalescing operator (??) al momento de asignarle un valor a las variables con respecto a lo recibido en la solicitud http. El operador retorna lo que tiene a la izquierda si existe y no es null, sino retorna lo que tiene a la derecha


endpoint put /calificacion{id}
    -modifiqué la forma en la que se reciben los datos desde el servidor porque con el método anterior (getParsedBody) los datos no se interpretaban correctamente y se recibían como null
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -ahora en vez de recibir el id de la calificación recibe el id del juego que se quiere calificar, realicé este cambio por como desarrollé el frontend
    -eliminé la validación de calificación propia porque por como se desarrolló el frontend, nunca se podría modificar una calificación que no es propia del usuario


endpoint delete /calificacion{id}
    -modifiqué la forma en la que se envían las respuestas para poder enviarlas en formato json facilitando la interpretacion en el frontend
    -ahora en vez de recibir el id de la calificación recibe el id del juego que se quiere calificar, realicé este cambio por como desarrollé el frontend
    -eliminé la validación de calificación propia porque por como se desarrolló el frontend, nunca se podría eliminar una calificación que no es propia del usuario