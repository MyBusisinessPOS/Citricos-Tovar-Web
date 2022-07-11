<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="3" align="center" style="font-size: 16; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">DEL {{$from}} AL {{$to}}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">TOTAL PAGO DE FRUTA</td>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">TOTAL VENDIDO DE FRUTA</td>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">UTILIDADES</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 30; font-weight: 400; border: 2px double #000000; padding: 8px;"> {{$totales['purchases']}}</td>
                <th align="center" style="background:#00b347; font-size: 30; text-align: center; vertical-align: center;" rowspan="9">{{$totales['sales']}}</th>
                <td align="center" style="background: #ADD8E6; font-size: 30; text-align: center; vertical-align: center;"  rowspan="9">{{$totales['utility']}}</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">TOTAL COMBUSTIBLE Y GASTOS VARIOS</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 30; font-weight: 400; border: 2px double #000000; padding: 8px;">{{$totales['expenses']}}</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">TOTAL NÓMINA</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 30; font-weight: 400; border: 2px double #000000; padding: 8px;"> 0.00</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">FLETES</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 30; font-weight: 400; border: 2px double #000000; padding: 8px;"> 00.00 </td>
            </tr>
            <tr>
                <td align="center" style="font-size: 12; background:#008000; color: white; border: 2px solid #000000; padding: 8px;">TOTAL GASTOS</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 30; font-weight: 400; border: 2px double #000000; padding: 8px;"> {{$totales['totales']}} </td>
            </tr>
        </tbody>
    </table>
</body>
</html>