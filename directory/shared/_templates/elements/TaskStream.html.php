<script>
if(window.XMLHttpRequest) {
    var xhr = new XMLHttpRequest();
    xhr.lastText = '';
     
    xhr.onerror = function() { console.log("[XHR] Fatal Error."); };
    xhr.onreadystatechange = function() {
        if(xhr.readyState > 2) {
            var message = xhr.responseText.replace(/^\s+/,"").substring(xhr.lastText.replace(/^\s+/,"").length);
            xhr.lastText = xhr.responseText;
            var objDiv = document.getElementById("divProgress");
            $(objDiv).append(message);
            objDiv.scrollTop = objDiv.scrollHeight;
        }
    };

    xhr.open("GET", "<?php echo $this['url']; ?>", true);
    xhr.send("Making request...");
}
</script>

<code style="display: block; border:2px solid #DDD; padding:10px; width:800px; height:400px; overflow:auto; background:#FAFAFA; border-radius: 0.7em;" id="divProgress"></code>
