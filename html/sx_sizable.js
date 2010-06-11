function sx_ajustSize() {
    var d = document.createElement('div');
    d.className = this.className;
    d.innerHTML = this.value.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/(\r\n|\r|\n)/g, '<br />') + '<br />';
    this.parentNode.appendChild(d);
    this.style.height = d.offsetHeight-2 + 'px';
    this.style.width = '100%';
    d.style.display = 'none';
    sx_remove(d);
}

function sx_remove(obj) {
    obj.parentNode.removeChild(obj);
}

function sx_appendTextareas() {
    taElements = document.getElementsByTagName("textarea");
    var taSizable = new Array();
    for (i = 0; i < taElements.length; i++) {
	if (taElements[i].className.indexOf("sizable") > -1) {
	    taSizable.push(taElements[i]);
	}
    }
    for (i = 0; i < taSizable.length; i++) {
	taSizable[i].onkeyup = sx_ajustSize;
	taSizable[i].onchange = sx_ajustSize;
	sx_fireEvent(taSizable[i],"keyup");
    }
}

function sx_fireEvent(obj, evt) {
  var fireOnThis = obj;
  if (document.createEvent) {
    var evObj = document.createEvent("MouseEvents");
    evObj.initEvent(evt, true, false);
    fireOnThis.dispatchEvent(evObj);
  }  else if (document.createEventObject) {
    fireOnThis.fireEvent("on" + evt);
  }
}
