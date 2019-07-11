
<div>
    <div style='background-color:green; height: 45px; display: flex; font-size: 25px; color:white'>
        PAYMENT INVOICE
    </div>
    <div>
        hi {{$user['name']}}<br/>
        Congratulations, you are now a premium member.

        <div style="padding-left: 20%; padding-right: 20%">
            <div style="border-bottom: 0.5px solid gray; border-top:0.5px solid gray;
                        padding:10px;
                        font-family:arial;
                        margin-top:50px;
                        display:flex;
                        justify-content:space-between;
                        background-color:rgb(4, 172, 54);
                    ">
                <span style="color:white">INVOICE</span>
            </div>
            <div style="
                            border-bottom:0.5px solid gray;
                            padding:10px;
                            font-family:arial;
                        ">
                <table style="height:140px;">
                    <tr >
                        <td style="color:gray">Name</td>
                        <td>{{$user['name']}}</td>
                    </tr>
                    <tr>
                        <td style="color:gray">Duration</td>
                        <td>{{$day}} days</td>
                    </tr>
                    <tr>
                        <td style="color:gray">Price</td>
                        <td>{{$price}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
