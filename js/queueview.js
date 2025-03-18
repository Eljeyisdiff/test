//for refresh
let isModalOpen = false;

// Open the cancellation dialog
document
  .querySelector(".cancel-ticket-button")
  .addEventListener("click", function () {
    isModalOpen = true;
    document.getElementById("dialog-cancel-queue").showModal();
  });

// Close the dialog
function closeDialog() {
  const dialog = document.getElementById("dialog-cancel-queue");
  isModalOpen = false;
  dialog.close();
}

setInterval(function () {
  if (!isModalOpen) {
    location.reload();
  }
}, 3000);


