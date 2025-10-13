<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Green Drive Connect Logs</title><meta name="description" content="VMS - Vehicle Management System by BDTask"><link rel="canonical" content="all">
    <link rel="shortcut icon" href="{{url('/')}}/storage/setting/ycsbDAa4bOn4ouFfSKkJ0o5C8prSzthSJEUHG078.png?v=1">  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card {
      margin: 20px auto;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .card-header {
      /*background-color: #009879;*/
      color: #ffffff;
      font-weight: bold;
      text-align: center;
    }
    .table{
      border: 1px solid #978b8b30;
    }

    .table th, .table td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table th, .table td {
      padding: 12px 15px;
      vertical-align: middle;
    }

    @media (max-width: 600px) {
      .table {
        font-size: 0.85em;
      }
      .card {
        margin: 10px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h4 class="text-dark">Log List</h4>
        <button id="downloadBtn" class="btn btn-dark">Download</button>
      </div>
      <div class="card-body">
        <table class="table table-border-left-1 table-border-right-1 table-hover">
          <thead class="table-dark">
            <tr>
              <th>SL</th>
              <th>Date</th>
              <th>In Time</th>
              <th>Out Time</th>
              <th>Total Online Hours</th>
            </tr>
          </thead>
          <tbody>
              @if(!empty($reports) && count($reports) > 0)
                    @foreach($reports as $key => $report)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ date('d-m-Y',strtotime($report->date)) ?? 'N/A' }}</td>
                            <td>{{ $report->in_time ?? 'N/A' }}</td>
                            <td>{{ $report->out_time ?? 'N/A' }}</td>
                            <td>{{ $report->total_time ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No records found</td>
                    </tr>
                @endif
            
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

 <script>
    document.getElementById('downloadBtn').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;

        // Capture the table element
        const element = document.querySelector('.card-body');

        html2canvas(element, {
            scale: 3,  // Increase scale to get higher resolution
            useCORS: true,  // Enable cross-origin images
        }).then((canvas) => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
            });

            // Get dimensions of PDF
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            // Calculate dimensions to maintain aspect ratio
            const imgWidth = pageWidth;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Check if the image height is larger than the page height
            let position = 0;
            if (imgHeight > pageHeight) {
                let heightLeft = imgHeight;

                while (heightLeft > 0) {
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                    position += pageHeight;
                    if (heightLeft > 0) {
                        pdf.addPage();
                    }
                }
            } else {
                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            }

            pdf.save('logs.pdf');
        });
    });
</script>

</body>

</html>


