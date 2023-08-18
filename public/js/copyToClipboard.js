function copyToClipboard(){
    var copyText = document.getElementById("copytext");
    var computerName = copyText.textContent;
    navigator.clipboard.writeText(computerName);
    alert("Copied " + computerName + " to clipboard.");
}