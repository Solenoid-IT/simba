{{ $app_name }}
<br><br>
Authorization Request for <b>{{ $type }}</b>

<br><br>

<table>
    <tbody>
        <tr>
            <th style="text-align: left;">
                IP
            </th>
            <td style="text-align: right;">
                {{ $client['ip']['address'] }}
                 • 
                {{ $client['ip']['country']['code'] }}
                 • 
                {{ $client['ip']['country']['name'] }}
                 • 
                <img class="country-flag" src="https://flagsapi.com/{{ $client['ip']['country']['code'] }}/flat/64.png" alt="" style="height: 20px;">
                 • 
                {{ $client['ip']['isp'] }}
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">
                Browser
            </th>
            <td style="text-align: right;">
                {{ $client['ua']['browser'] }}
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">
                OS
            </th>
            <td style="text-align: right;">
                {{ $client['ua']['os'] }}
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">
                HW
            </th>
            <td style="text-align: right;">
                {{ $client['ua']['hw'] }}
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">
                DateTime
            </th>
            <td style="text-align: right;">
                {{ gmdate( 'c' ) }}
            </td>
        </tr>

        <tr>
            <th style="text-align: left;">
                Action
            </th>
            <td style="text-align: right;">
                <div style="padding: 10px;">
                    <a href="{{ "$endpoint_url/accept" }}" target="_blank" style="padding: 10px 20px; background-color: green; color: #ffffff; text-decoration: none; border-radius: 4px;">
                        Run
                    </a>
                    <a href="{{ "$endpoint_url/decline" }}" target="_blank" style="margin-left: 20px; padding: 10px 20px; background-color: red; color: #ffffff; text-decoration: none; border-radius: 4px;">
                        Cancel
                    </a>
                </div>
            </td>
        </tr>
    </tbody>
</table>

{!! $custom_text !!}