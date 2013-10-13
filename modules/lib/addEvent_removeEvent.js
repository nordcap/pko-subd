<script language="JavaScript" type="text/javascript">
//���������� ���������� � �������� ������� ��� DOM-���������

function addEvent(element,type, handler){	//���������� ������� ����������� ������� ����������� ��������������
if (!handler.$$guid) handler.$$guid = addEvent.guid++;
 //�������� ���-������� ����� ��������� ��� ��������
if (!element.events) element.events = {};
 //�������� ���-������� ����� ������� ��� ������ ���� �������-�������
var handlers = element.events[type];
if (!handlers){	handlers = element.events[type] = {};
	//���������� ������������� ����������� ������� (���� �� ����������)
	if (element["on" + type]){
		handlers[0] = element["on" + type];
	}
}
//���������� ����������� ������� � ���-�������
handlers[handler.$$guid] = handler;

//���������� ����������� ����������� ������� ��� ���������� ���� ������
element["on" + type] = handleEvent;
};


//������� ������������ ��� �������� ���������� ���������������
addEvent.guid = 1;

function removeEvent(element,type, handler) {//�������� ����������� ������� �� ���-�������
if (element.events && element.events[type]){	delete element.events[type][handler.$$guid];}
}

function handleEvent(event){var returnValue = true;
//������ ������� ������� (IE ���������� ���������� ������ �������)
event = event || fixEvent(window.event);
//��������� ������ �� ���-������� ������������ �������
var handlers = this.events[event.Type];
//���������� ������� ����������� �������
for (var i in handlers) {
	this.$$handleEvent = handlers[i];
	if (this.$$handleEvent(event) === false){		returnValue = false;	}
}
return returnValue;}

//���������� � IE-������� ��������� ��������� �������
function fixEvent(event){//���������� ����������� (W3C) ������� �������
event.preventDefault = fixEvent.preventDefault;
event.stopPropagation = fixEvent.stopPropagation;
return event;};

fixEvent.preventDefault = function(){	this.returnValue = false;};

fixEvent.stopPropagation = function(){	this.cancelBibble = true;};
</script>
