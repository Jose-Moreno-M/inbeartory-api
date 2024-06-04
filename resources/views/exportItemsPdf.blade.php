<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>

        body{
            font-family: Arial, Helvetica, sans-serif;
        }

        table, caption, th, td{
            border: 1px solid black;
            border-collapse: collapse
        }

        table{
            width: 100%;
            /* table-layout: fixed; */
        }

        p{text-align: left}

        .no_border{border: none;}

        .m_left{margin-left: 5%;}

        #logo{
            position: absolute;
            text-align: right;
            width: 5%;
            margin-top: 2%;
            margin-left: 1%;
        }

    </style>
</head>
<body>
    <img src="{{public_path().'/storage/images/assets/logo_unisierra.png'}}" alt="" id="logo">

    <table>
        <thead>
            <tr>
                <th colspan="5" class="no_border">
                <h3 id="t1"><i>UNIVERSIDAD DE LA SIERRA</i></h3>
                <h4 id="t2">INVENTARIO FÍSICO DE LOS BIENES MUEBLES E INMUEBLES, EQUIPO DE TRANSPORTE, EQUIPOS DE LABORATORIO</h4>
                </th>
            </tr>
            <tr>
                <th class="no_border" colspan="2">
                    <!--<p class="m_left">No. de Vale: <ins>US0038</ins></p>-->
                    <P class="m_left">Nombre: <ins>{{$user_name}}</ins></P>
                    <p class="m_left">Adscripción: <ins>Secretaría General Académica</ins></p>
                </th>
                <th class="no_border"></th>
                <th class="no_border" colspan="2">
                    <p class="m_left">C.U.R.P.: <ins>{{$user_curp}}</ins></p>
                    <P class="m_left">Puesto: <ins>{{$user_position}}</ins></P>
                    <p class="m_left">Ubicación del bien: <ins>{{$area_name}}</ins></p>
                </th>
            </tr>
        </thead>
    </table>
<table>
    <thead>
        <tr>
            <th>No. Inventario</th>
            <th>Descripción del bien</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Serie</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item )
            <tr>
                <td style="text-align: center">{{$item->inventory_number}}</td>
                <td>{{$item->description}}</td>
                <td>{{$item->brand}}</td>
                <td>{{$item->model}}</td>
                <td>{{$item->serie}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
