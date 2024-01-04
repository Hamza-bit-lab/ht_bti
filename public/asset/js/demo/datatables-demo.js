// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    "order": [[4, "desc"]],
    "columnDefs": [
      {
        "type": "date",
        "targets": 4
      }
    ],
    "filter": false,
    "lengthChange": false,
  });
});
