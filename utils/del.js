function allButtonsDisabled(toggle) {
  // $("button").each(function(){console.log($(this).text())})

  if (toggle) {
    console.log("GONNA DISABLE ALL BUTTONS");
    $("button").attr("disabled", true);
  } else {
    console.log("GONNA enable ALL BUTTONS");
    $("button").removeAttr("disabled");
  }
}
