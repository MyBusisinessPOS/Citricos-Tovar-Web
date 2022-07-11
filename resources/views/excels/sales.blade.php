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
                <th colspan="9" align="center" style="background:#008000; color: white; border: 2px solid #000000; padding: 8px;">DEL {{$from}} AL {{$to}}</th>
            </tr>
        </thead>
        <tbody> 
            @php
                $counter = 0
            @endphp
            @foreach ($sales as $key => $sale)
                @php
                    $counter++;
                @endphp
                <tr>
                    <td align="left" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 18; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>CLIENTE:</strong></td>
                    <td colspan="8" align="center" style="background: {{ $counter % 2 == 0 ? '#F3AF85': '#AEAAAA' }}; font-size: 18; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>{{$key}}</strong></td>
                </tr>
                <tr>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>FECHA</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>REJAS</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>PESO</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>PRECIO</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>TOTAL</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>PROMEDIO REJAS</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>CUENTA TOTAL</strong></td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px; width: 10px;">&nbsp;</td>
                    <td style="font-size: 12; font-weight: 400; border: 2px double #000000; padding: 8px; width: 10px;">&nbsp;</td>
                </tr>
                @php
                    $total = 0;
                @endphp

                @foreach ($sale as $ky => $item)
                @php
                    if ($item->weight > 0) {
                        $total = $total + floatval($item->weight) * floatval($item->price);
                    }

                    $average = 0;
                    if ($item->weight > 0 && $item->boxs) {
                        $average = floatval($item->weight) / floatval($item->boxs);
                    }
                @endphp
                    <tr>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$item->date}}</strong></td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$item->boxs}}</strong></td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$item->weight}}KG</strong></td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$item->price}}</strong></td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{floatval($item->weight) * floatval($item->price)}}</strong></td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$average}}</strong></td>
                        <td style="font-size: 20; font-weight: 400; border: 2px double #000000; padding: 8px;">
                        <strong>
                           @if ($item === end($sale))
                               {{$total}}
                           @endif                       
                        </strong>
                        </td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;">&nbsp;</td>
                        <td style="font-size: 16; font-weight: 400; border: 2px double #000000; padding: 8px;">&nbsp;</td>                        
                    </tr>
                @endforeach               
            @endforeach
            <tr>
                <td colspan="9" align="center" style="background: #008000; font-size: 20; font-weight: 400; border: 2px solid #000000; padding: 8px;"><strong>TOTALES</strong></td>
            </tr>            
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">REJAS DE PRIMERA</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">REJAS DE SEGUNDA</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">REJAS DE TERCERA</td>
            </tr>            
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{ $totales[0] ? $totales[0]['boxs'] : 0}}</strong></td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{ $totales[1] ? $totales[1]['boxs'] : 0}}</strong></td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{ $totales[2] ? $totales[2]['boxs'] : 0}}</strong></td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">TONELADAS PRIMERA</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">TONELADAS SEGUNDA</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">TONELADAS TERCERA</td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$totales[0] ? $totales[0]['weight'] : 0}}KG</strong></td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$totales[1] ? $totales[1]['weight'] : 0}}KG</strong></td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;"><strong>{{$totales[2] ? $totales[2]['weight'] : 0}}KG</strong></td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">PROMEDIO DE PESO</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">PROMEDIO DE PESO</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">PROMEDIO DE PESO</td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">
                    <strong>@if (isset($totales[0]) && floatval($totales[0]['weight']) && floatval($totales[0]['boxs']))
                        {{floatval($totales[0]['weight']) / floatval($totales[0]['boxs'])}}
                    @else
                    0
                    @endif
                    </strong>
                </td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">
                    <strong>@if (isset($totales[1]) && floatval($totales[1]['weight']) && floatval($totales[1]['boxs']))
                        {{floatval($totales[1]['weight']) / floatval($totales[1]['boxs'])}}
                    @else
                    0
                    @endif
                    </strong>
                </td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">
                    <strong>@if (isset($totales[2]) && floatval($totales[2]['weight']) && floatval($totales[2]['boxs']))
                        {{floatval($totales[2]['weight']) / floatval($totales[2]['boxs'])}}
                    @else
                    0
                    @endif
                    </strong>
                </td>
            </tr> 
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">TOTAL VENDIDO PRIMERA</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">TOTAL VENDIDO SEGUNDA</td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">TOTAL VENDIDO TERCERA</td>
            </tr>  
            <tr>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">
                    <strong>@if (isset($totales[0]))
                        {{floatval($totales[0]['total'])}}
                    @else
                    0
                    @endif
                    </strong>
                </td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">
                    <strong>@if (isset($totales[1]))
                        {{floatval($totales[1]['total'])}}
                    @else
                    0
                    @endif
                    </strong>
                </td>
                <td align="center" colspan="3" style="background:#008000; font-size: 18; font-weight: 400; border: 2px double #000000; padding: 8px;">
                    <strong>@if (isset($totales[2]))
                        {{floatval($totales[2]['total'])}}
                    @else
                    0
                    @endif
                    </strong>
                </td>
            </tr>          
        </tbody>          
    </table>  
   
    
</body>
</html>