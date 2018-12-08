/* Open the sidenav */
function openNav() {

    //document.getElementById("mySidenav").style.width = "100%";

    $("div.sidenav").css("display", "block");
    $("body").css("overflow", "hidden");
    $("div.content-page").css("display", "none");
}

/* Close/hide the sidenav */
function closeNav() {

    //document.getElementById("mySidenav").style.width = "0";

    $("div.sidenav").css("display", "none");
    $("body").css("overflow", "auto");
    $("div.content-page").css("display", "flex");
}