document.addEventListener("DOMContentLoaded", function () {
  let isModalOpen = false;

  //refresh the page every 5 seconds
  setInterval(function () {
    if (!isModalOpen) {
      location.reload();
    }
  }, 3000);

  const cancelTicketBtn = document.querySelector(".cancel-ticket-btn");
  const cancelDialog = document.getElementById("dialog-cancel-ticket");

  //CANCEL DIALOG
  cancelTicketBtn.addEventListener("click", function () {
    isModalOpen = true;
    cancelDialog.showModal();
  });

  //the x button to close the dialog
  const closeButtons = document.querySelectorAll(".close-button");
  closeButtons.forEach((button) => {
    button.addEventListener("click", function () {
      isModalOpen = false;
      button.closest("dialog").close();
    });
  });

  //cancel button to close the dialog
  const cancelButtons = document.querySelectorAll(".button-left.cancel");
  cancelButtons.forEach((button) => {
    button.addEventListener("click", function () {
      isModalOpen = false;
      button.closest("dialog").close();
    });
  });

  //cancel ticket from queue
  const cancelQueueButton = document.getElementById("cancelQueueButton");
  const dialog = document.getElementById("dialog-cancel-queue");
  const hiddenInput = document.getElementById("hiddenQueueNumber");

  if (cancelQueueButton) {
    cancelQueueButton.addEventListener("click", function () {
      // Get the queue number from the div's value attribute
      const queueNumber = cancelQueueButton.getAttribute("value");

      // Set the value of the hidden input
      hiddenInput.value = queueNumber;
      isModalOpen = true;
      // Open the dialog
      dialog.showModal();
    });
  }

  //UPDATE THE OFFICE TO CLOSED
  const closeOfficeBtn = document.querySelector(".close-office-btn");
  if (closeOfficeBtn) {
    closeOfficeBtn.addEventListener("click", function () {
      isModalOpen = true;
      document.getElementById("dialog-close-office").showModal();
    });
  }

  //UPDATE THE OFFICE TO CLOSED
  const openOfficeBtn = document.querySelector(".open-office-btn");
  if (openOfficeBtn) {
    openOfficeBtn.addEventListener("click", function () {
      isModalOpen = true;
      document.getElementById("dialog-open-office").showModal();
    });
  }

  //message when the office is opened
  const openMessageDiv = document.getElementById("open-message");
  if (openMessageDiv.innerHTML.trim() !== "") {
    // Set a timeout to hide the message after 3 seconds (3000 milliseconds)
    setTimeout(() => {
      openMessageDiv.style.display = "none"; // Hide the message
    }, 3000);
  }

  //done button dialog
  document
    .querySelector(".done-ticket-btn")
    .addEventListener("click", function () {
      isModalOpen = true;
      document.getElementById("dialog-queue-done").showModal();
    });

    //TAKE A BREAK MODAL
    const takeBreakBtn = document.querySelector(".break-btn");
    const takeBreakDialog = document.getElementById("dialog-take-break");
    if (takeBreakBtn) {
      takeBreakBtn.addEventListener("click", function () {
        isModalOpen = true;
        takeBreakDialog.showModal();
      });
    }

    //RESUME WORK MODAL
    const resumeWorkBtn = document.querySelector(".queue-btn");
    const resumeWorkDialog = document.getElementById("dialog-resume-break");
    if (resumeWorkBtn) {
      resumeWorkBtn.addEventListener("click", function () {
        isModalOpen = true;
        resumeWorkDialog.showModal();
      });
    }
});
