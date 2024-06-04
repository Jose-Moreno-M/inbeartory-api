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
        
        img{
            width: 100%;
            height: 10%;
        }
        
        .footer{
            margin-bottom: 0%;
            margin-top: 100%;
        }

    </style>
</head>
<body>
    <img src="{{public_path().'/storage/images/assets/header'}}" alt="header">
    <p><b>A quien corresponda</b></p>
    <p><b>Jefe de Departamento de Recursos Materiales</b></p>
    <p>PRESENTE</p>

    <p>
        Por medio del presente me permito informar que, {{$motive}}. Por lo tanto, al no estar bajo mi resguardo y cuidado a partir de la fecha de este oficio, le solicito retire de mi resguardo de inventario los siguientes equipos que estaban asignados al {{$area_name}}.
    </p>
    <table>
        <thead>
            <tr>
                <th>Marca</th>
                <th>Descripción</th>
                <th>No. Inv.</th>
                <th>No. Serie</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item )
                <tr>
                    <td>{{$item->brand}}</td>
                    <td>{{$item->description}}</td>
                    <td>{{$item->inventory_number}}</td>
                    <td>{{$item->serie}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Se anexa evidencia fotográfica de los equipos asignados, en el Anexo 1.</p>
    <p>Sin más por el momento, le envío un cordial saludo.</p>

    <p><b>{{$user->name}}</b></p>
    <p>{{$user->position}}</p>
    <p>Encargado de {{$area_name}}</p>
    <img src="{{public_path().'/storage/images/assets/footer'}}" alt="footer" claass="footer">
</body>
</html>
