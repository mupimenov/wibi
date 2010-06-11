function wb_appendTags() {
    tElement = document.getElementById("all-tags");
    aLinks = tElement.getElementsByTagName("a");
    for (i = 0; i < aLinks.length; i++) {
        aLinks[i].onclick = wb_addTag;
    }
}

function wb_addTag() {
    taTags = document.getElementById("page-tags");
    tagName = this.innerHTML;
    tagsString = taTags.value.replace(/^\s+|\s+$/g,"");
    if (tagsString) {
        if (tagsString.lastIndexOf(',') == tagsString.length-1) {
            taTags.value = tagsString + ' ' + tagName;
        } else {
            taTags.value = tagsString + ', ' + tagName;
        }
    } else {
        taTags.value = tagName;
    }
}
