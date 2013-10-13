<script language="JavaScript" type="text/javascript">
//библиотека добавления и удаления события для DOM-элементов

function addEvent(element,type, handler){	//присвоение каждому обработчику события уникального идентификатора
if (!handler.$$guid) handler.$$guid = addEvent.guid++;
 //создание хэш-таблицы видов изменений для элемента
if (!element.events) element.events = {};
 //создание хэш-таблицы видов событий для каждой пары элемент-событие
var handlers = element.events[type];
if (!handlers){	handlers = element.events[type] = {};
	//сохранение существующего обработчика событий (если он существует)
	if (element["on" + type]){
		handlers[0] = element["on" + type];
	}
}
//сохранение обработчика событий в хэш-таблице
handlers[handler.$$guid] = handler;

//назначение глобального обработчика события для выполнения всей работы
element["on" + type] = handleEvent;
};


//счетчик используемый для создания уникальных идентификаторов
addEvent.guid = 1;

function removeEvent(element,type, handler) {//удаление обработчика события из хэш-таблицы
if (element.events && element.events[type]){	delete element.events[type][handler.$$guid];}
}

function handleEvent(event){var returnValue = true;
//захват объекта события (IE использует глобальный объект события)
event = event || fixEvent(window.event);
//получение ссылки на хэш-таблицу обработчиков событий
var handlers = this.events[event.Type];
//выполнение каждого обработчика события
for (var i in handlers) {
	this.$$handleEvent = handlers[i];
	if (this.$$handleEvent(event) === false){		returnValue = false;	}
}
return returnValue;}

//добавление к IE-объекту некоторых упущенных методов
function fixEvent(event){//добавление стандартных (W3C) методов событий
event.preventDefault = fixEvent.preventDefault;
event.stopPropagation = fixEvent.stopPropagation;
return event;};

fixEvent.preventDefault = function(){	this.returnValue = false;};

fixEvent.stopPropagation = function(){	this.cancelBibble = true;};
</script>
