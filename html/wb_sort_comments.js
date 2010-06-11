function toggleSorting(id) {
    var comments = document.getElementById( id );    
    for (i = comments.children.length - 1; i >= 0 ; i--) {
        var c = comments.children[i];
        comments.removeChild(comments.children[i]);
        comments.appendChild( c );        
    }
}

