
<html>


<body class="viewframe">
	<table border="0" cellpadding="0" cellspacing="0" style="padding-top: 16px; background-color: #F1F1F1; font-family:Verdana, Arial,sans-serif; color: #454748; width: 100%; border-collapse:separate;">
		<tbody>
			<tr>
				<td align="center">
					<table border="0" cellpadding="0" cellspacing="0" width="590" style="padding: 24px; background-color: white; color: #454748; border-collapse:separate;">
						<tbody>
							<tr>
								<td align="center" style="min-width: 590px;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: white; padding: 0; border-collapse:separate;">
										<tbody>
											<tr>
												<td valign="middle"> <span style="font-size: 10px;">  Order Successfull. License Available</span>
													<br>
                                                    <span style="font-size: 20px; font-weight: bold;">
                                                        {{ $data['quotationnumber'] }}
                                                    </span>
                                                </td>
												<td valign="middle" align="right"> <img style="padding: 0px; margin: 0px; height: 48px;" src="{{ asset('public\frontside\dist\img\logo.png') }}" > </td>
											</tr    >
											<tr>
												<td colspan="2" style="text-align:center;">
													<hr width="100%" style="background-color:rgb(204,204,204);border:medium none;clear:both;display:block;font-size:0px;min-height:1px;line-height:0; margin:4px 0px 32px 0px;"> </td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td style="padding: 0">
									<div style="font-size:13px; font-family:&quot;Lucida Grande&quot;, Helvetica, Verdana, Arial, sans-serif; margin:0px; padding:0px">
                                        Your Quotation is approved. Following are the assigned licenses <br>
                                            @foreach($data['licenses'] as $product => $licences)
                                            <h3>{{ $product }}</h3>
                                                <ul>
                                                    @foreach($licences[0] as $license)
                                                    <li>{{ $license->license_key }}</li>
                                                    @endforeach
                                                </ul>
                                            @endforeach

                                    </div>
									<div style="margin: 0px; padding: 0px; font-size:13px;"> Best regards, </div>
									<div>&nbsp;</div>
									<div style="font-size: 13px;">
										<div>
                                            <span data-o-mail-quote="1">-- <br data-o-mail-quote="1">TIMmunity</span>
                                        </div>
									</div>
								</td>
							</tr>
							<tr>
								<td style="padding: 0; font-size:11px;">
									<hr width="100%" style="background-color:rgb(204,204,204);border:medium none;clear:both;display:block;font-size:0px;min-height:1px;line-height:0; margin: 32px 0px 4px 0px;"> <b>TIMmunity</b>
									<br>

								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>

		</tbody>
	</table>
</body>

</html>
