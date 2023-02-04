<div class="col">
    <div class="card">
        <div class="card-header">Data Pemakaian</div>
        <div class="card-body">
            <table class="datatables" width="100%">
                <thead>
                    <tr>
                        <th>No Pelanggan</th>
                        <th>Nama</th>
                        <th>Pemakaian</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $p)
                        <tr>
                            <td>{{ $p->username }}</td>
                            <td>{{ $p->nama }}</td>
                            <td id="meter_td_{{ $p->id }}">
                                {{ $p->meter }} m<sup>3</sup>
                            </td>
                            <td id="total_td_{{ $p->id }}">{{ formatRupiah($p->total) ?? formatRupiah(0) }}</td>
                            <td id="pembayaran_td_{{ $p->id }}">
                                @if($p->status == 0 && $p->meter != 0)
                                    <span class="badge badge-warning">Menunggu Pembayaran</span>
                                @elseif($p->status == 0 && $p->meter == 0)
                                    <span class="badge badge-warning">Belum Input Meter</span>
                                @else
                                    <span class="badge badge-success">Lunas</span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.datatables').DataTable();
    });
    function inputPemakaian(e){
        var id = $(e).attr('data-id');
        var meter = $(e).val();
        $.ajax({
            type: 'post',
            url: './pemakaian/input',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                meter: meter,
                tahun: '{{ $tahunPilih }}',
                bulan: '{{ $bulanPilih }}'
            },
            cache: false,
            success: function(msg){
                var data = JSON.parse(msg);
                if(data.status == 1){
                    $("#total_td_"+id).html(data.totalRp);
                    if(data.total == 0){
                        $("#pembayaran_td_"+id).html('<span class="badge badge-warning">Belum Input Meter</span>');
                    }else{
                        $("#pembayaran_td_"+id).html('<a href="./pemakaian/bayar/'+id+'" class="btn btn-primary">Bayar</a>');
                    }
                }else{
                    Swal.fire(
                        "Gagal!",
                        data.msg,
                        "error",
                    )
                }
                $('.datatables').DataTable();
            }
        });
    }
    function bayarPemakaian(btn,id){
        if(confirm("Apakah anda yakin ingin membayar?") == false) return false;
        $(btn).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`).attr('disabled',true);
        $.ajax({
            type: 'post',
            url: './pemakaian/bayar',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                tahun: '{{ $tahunPilih }}',
                bulan: '{{ $bulanPilih }}'
            },
            cache: false,
            success: function(msg){
                $(btn).html('Bayar').attr('disabled',false);
                var data = JSON.parse(msg);
                if(data.status == 1){
                    $("#meter_td_"+id).html(`${data.meter} m<sup>3</sup>`);
                    $("#pembayaran_td_"+id).html('<span class="badge badge-success">Lunas</span>');
                }else{
                    Swal.fire(
                        "Gagal!",
                        data.msg,
                        "error",
                    )
                }
                $('.datatables').DataTable();
            }
        });
    }
</script>