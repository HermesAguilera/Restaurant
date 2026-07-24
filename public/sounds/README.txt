Sonidos de alerta para los monitores de cocina.

Cada sección usa su propio archivo (así cada cocina suena distinto):

  new-order-general.mp3   -> Comida General
  new-order-china.mp3     -> Comida China
  new-order-pizza.mp3     -> Pizza

Coloca cada archivo con ese nombre exacto en esta carpeta.

Si falta el archivo de una sección (o hay algún error al cargarlo), el sistema
reproduce un pitido sintético de respaldo (Web Audio API) que YA es distinto por
sección, por lo que las cocinas siguen diferenciándose aunque no pongas los mp3.
