<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table style="border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="10" align="center" style="background:#008000; color: white; border: 2px solid #000000; padding: 8px;">DEL {{$from}} AL {{$to}}</th>
            </tr>
            <tr>
                <th colspan="10" align="center">&nbsp;</th>
            </tr>
        </thead>
        <tbody>           
            @php
                $counter = 0
            @endphp
            @foreach ($purchases as $key => $item)                
                @php
                    $counter++;
                @endphp
                <tr>
                    <th align="center" colspan="10" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 18; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>{{$key}}</strong></th>
                </tr>            
                <tr>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>REJAS</strong></td>
                    <td align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400;border: 2px solid #000000; padding: 8px;"><strong>DINERO</strong></td>
                    <td align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400;border: 2px solid #000000; padding: 8px;"><strong>PROMEDIO COMPRA</strong></td>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>COMBUSTIBLE</strong></td>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>NOMINA</strong></td>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>GASTOS</strong></td>
                </tr>
                @foreach ($item as $key => $row)
                <tr>
                    <td style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;">{{$row->product}}</td>
                    <td style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 800; border: 2px solid #000000; padding: 8px;">{{$row->quantity}}</td>
                    <td style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 800; border: 2px solid #000000; padding: 8px;">{{$row->total}}</td>
                    <td style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; font-weight: 800; border: 2px solid #000000; padding: 8px;">{{floatval($row->total) / floatval($row->quantity)}}</td>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; border: 2px solid #000000; padding: 8px;">0.00</td>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; border: 2px solid #000000; padding: 8px;">0.00</td>
                    <td colspan="2" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 14; border: 2px solid #000000; padding: 8px;">0.00</td>
                </tr>    
                @endforeach                    
            @endforeach               
        </tbody>       
        <tfoot>
            <tr>
                <th colspan="10" align="center" style="background:#008000; color: white; border: 2px solid #000000; padding: 8px; font-size: 18;">TOTALES</th>
            </tr>
            @php
               
                $counter = 0;
            @endphp
            @foreach ($totales as $key => $item)
                @php
                    $counter++;
                    $gas = 0;
                    $payroll = 0;
                    $bills = 0;
                    if ($counter == 1) {
                        $gas = $item['gas'];
                        $payroll = $item['payroll'];
                        $bills = $item['bills'];
                    } else {

                    }
                @endphp
                <tr>
                    <td style="font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>{{$key}}</strong></td>
                    <td style="font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>{{$item['quantity']}}</strong></td>
                    <td style="font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>{{$item['total']}}</strong></td>
                    <td style="font-size: 14; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>{{$item['total'] / $item['quantity']}}</strong></td>
                    <td colspan="2" align="center" style="font-size: 14; border: 2px solid #000000; padding: 8px;"><strong>{{$gas}}</strong></td>
                    <td colspan="2" align="center" style="font-size: 14; border: 2px solid #000000; padding: 8px;"><strong>{{$payroll}}</strong></td>
                    <td colspan="2" align="center" style="font-size: 14; border: 2px solid #000000; padding: 8px;"><strong>{{$bills}}</strong></td>
                </tr>
            @endforeach
        </tfoot> 
    </table>  
   
    
</body>
</html>