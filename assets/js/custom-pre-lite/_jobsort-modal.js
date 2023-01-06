var modal = document.getElementById("infoModal");

var span = document.getElementsByClassName("close")[0];

function openModal(inner_html){
	$("#infoModal").find(".modal-inner-content").html(inner_html)
	$('#infoModal').css('display', 'block')
	
}

window.onclick = function(event) {
  if (event.target != modal) {
    $("#infoModal").css("display", "none");
  }
}

function openWelcomeModal(){
	
	welcomeModal = $("<div\>")
	welcomeModal.append("<br>")
	welcomeModal.append('<h4>Welcome to Job Routing</h4>');  
	welcomeModal.append("<br>")
	welcomeModal.append('<h5>1. Moving a Job to a staff member</h5>'); 
	welcomeModal.append("<p>To move a job to a staff member, simply expand the staff member's jobs and drag the job into their job list.</p>"); 
	welcomeModal.append("<img class='demonstation1' src='assets/img/demonstration1.gif'>"); 
	
	openModal(welcomeModal)
}


document.addEventListener("DOMContentLoaded", function(){
    //openWelcomeModal()
});