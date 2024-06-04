<table>
    <thead>
        <tr>
            <th style="text-align: center" colspan="5"><h3><i>UNIVERSIDAD DE LA SIERRA</i></h3></th>
        </tr>
        <tr>
            <th style="text-align: center" colspan="5"><h4>INVENTARIO FÍSICO DE LOS BIENES MUEBLES E INMUEBLES, EQUIPO DE TRANSPORTE, EQUIPOS DE LABORATORIO</h4></th>
        </tr>
        <tr>
            <!--<th colspan="3"><p>No. de Vale: <ins>US0038</ins></p></th>-->
            <th colspan="3"><p style="color:white">.</p></th>
            <th colspan="2"><p>C.U.R.P.: <ins>{{$user_curp}}</ins></p></th>
        </tr>
        <tr>
            <th colspan="3"><P>Nombre: <ins>{{$user_name}}</ins></P></th>
            <th colspan="2"><P>Puesto: <ins>{{$user_position}}</ins></P></th>
        </tr>
        <tr>
            <th colspan="3"><p>Adscripción: <ins>Secretaría General Académica</ins></p></th>
            <th colspan="2"><p>Ubicación del bien: <ins>{{$area_name}}</ins></p></th>
        </tr>
        <tr style="text-align: center">
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
                <td style="text-align: center; width:150px">{{$item->inventory_number}}</td>
                <td style="width: 150px; text-align:left">{{$item->description}}</td>
                <td style="width: 150px; text-align:left">{{$item->brand}}</td>
                <td style="width: 150px; text-align:left">{{$item->model}}</td>
                <td style="width: 150px; text-align:left">{{$item->serie}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
