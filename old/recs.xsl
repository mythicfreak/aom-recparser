<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:fn="http://www.w3.org/2005/02/xpath-functions">
	<xsl:template name="GetGod">
		<xsl:param name="godID">12</xsl:param>
		<xsl:if test="$godID = 0">Zeus</xsl:if>
		<xsl:if test="$godID = 1">Poseidon</xsl:if>
		<xsl:if test="$godID = 2">Hades</xsl:if>
		<xsl:if test="$godID = 3">Isis</xsl:if>
		<xsl:if test="$godID = 4">Ra</xsl:if>
		<xsl:if test="$godID = 5">Set</xsl:if>
		<xsl:if test="$godID = 6">Odin</xsl:if>
		<xsl:if test="$godID = 7">Thor</xsl:if>
		<xsl:if test="$godID = 8">Loki</xsl:if>
		<xsl:if test="$godID = 9">Kronos</xsl:if>
		<xsl:if test="$godID = 10">Oranos</xsl:if>
		<xsl:if test="$godID = 11">Gaia</xsl:if>
		<xsl:if test="$godID = 12">Random All</xsl:if>
		<xsl:if test="$godID = 13">Random Greek</xsl:if>
		<xsl:if test="$godID = 14">Random Norse</xsl:if>
		<xsl:if test="$godID = 15">Random Egyptian</xsl:if>
		<xsl:if test="$godID = 16">Random Atlantean</xsl:if>
		<xsl:if test="$godID = 17">Nature</xsl:if>
	</xsl:template>
	
	<xsl:template name="GetGameType">
		<xsl:param name="GameType" />
		<xsl:if test='$GameType = 0'>0</xsl:if>
		<xsl:if test='$GameType = 1'>1</xsl:if>
		<xsl:if test='$GameType = 2'>Supremacy</xsl:if>
		<xsl:if test='$GameType = 3'>3</xsl:if>
	</xsl:template>
	
	<xsl:template name="Team_Template">
		<xsl:param name="num">0</xsl:param>
		<xsl:variable name='NumPlayers' select='NumPlayers' />
		<xsl:if test="not($num = $NumPlayers div 2)">
			<tr >
				<td align='left'>
					<table width='300px' border='1' align='left' cellpadding='0' cellspacing='0'>
						<tr>
							<td width='51' align='center'><img src='img/{//Player[@ClientID = $num]/Civilization}.gif' /></td>
							<td align='left' valign='center'>
								<b><xsl:value-of select="//Player[@ClientID = $num]/Name" /></b><br />
								<b><xsl:value-of select="//Player[@ClientID = $num]/Rating" /></b>
							</td>
						</tr>
					</table>
				</td>
				<td align='center'>vs</td>
				<td align='right'>
					<table width='300px' border='1' align='left' cellpadding='0' cellspacing='0'>
						<tr>
							<xsl:if test="MapSize = 0">
								<xsl:variable name='OpponentID' select='$num + $NumPlayers div 2' />
								<td width='51' align='center'><img src='img/{//Player[@ClientID = $OpponentID]/Civilization}.gif' /></td>
								<td align='left' valign='center'>
									<b><xsl:value-of select="//Player[@ClientID = $OpponentID]/Name" /></b><br />
									<b><xsl:value-of select="//Player[@ClientID = $OpponentID]/Rating" /></b>
								</td>
							</xsl:if>
							<xsl:if test="MapSize = 1">
								<xsl:variable name='OpponentID' select='(1 + $num) mod ($NumPlayers div 2) + ($NumPlayers div 2)' />
								<td width='51' align='center'><img src='img/{//Player[@ClientID = $OpponentID]/Civilization}.gif' /></td>
								<td align='left' valign='center'>
									<b><xsl:value-of select="//Player[@ClientID = $OpponentID]/Name" /></b><br />
									<b><xsl:value-of select="//Player[@ClientID = $OpponentID]/Rating" /></b>
								</td>
							</xsl:if>
						</tr>
					</table>
				</td>
			</tr>
			<xsl:call-template name="Team_Template">
				<xsl:with-param name="num">
					<xsl:value-of select="$num + 1" />
				</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="Team_Template_2">
		<xsl:param name="num">0</xsl:param>
		<xsl:variable name='NumPlayers' select='NumPlayers' />
		<xsl:if test="not($num = $NumPlayers div 2)">
			<xsl:value-of select="//Player[@ClientID = $num]/Name" />
			(<xsl:call-template name="GetGod">
				<xsl:with-param name="godID">
					<xsl:value-of select="//Player[@ClientID = $num]/Civilization" />
				</xsl:with-param>
			</xsl:call-template>)
			 vs 
			<xsl:if test="MapSize = 0">
				<xsl:variable name='OpponentID' select='$num + $NumPlayers div 2' />
				<xsl:value-of select="//Player[@ClientID = $OpponentID]/Name" />
				(<xsl:call-template name="GetGod">
				<xsl:with-param name="godID">
					<xsl:value-of select="//Player[@ClientID = $OpponentID]/Civilization" />
				</xsl:with-param>
			</xsl:call-template>)<br />
			</xsl:if>
			<xsl:if test="MapSize = 1">
				<xsl:variable name='OpponentID' select='(1 + $num) mod ($NumPlayers div 2) + ($NumPlayers div 2)' />
				<xsl:value-of select="//Player[@ClientID = $OpponentID]/Name" />
				(<xsl:call-template name="GetGod">
				<xsl:with-param name="godID">
					<xsl:value-of select="//Player[@ClientID = $OpponentID]/Civilization" />
				</xsl:with-param>
			</xsl:call-template>)<br />
			</xsl:if>
			<xsl:call-template name="Team_Template_2">
				<xsl:with-param name="num">
					<xsl:value-of select="$num + 1" />
				</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>

	<xsl:template match="GameSettings">
		<html>
			<head>
				<title>Recorded Game</title>
			</head>
			<body>
				<table border='0' align='center' cellpadding='2' cellspacing='0'>
					<tr>
						<td align='right'><b>Game Type: </b></td>
						<td />
						<td align='left'>
							<xsl:call-template name="GetGameType">
								<xsl:with-param name="GameType">
									<xsl:value-of select="GameType" />
								</xsl:with-param>
							</xsl:call-template>
						</td>
					</tr>
					<tr>
						<td align='right'><b>Map: </b></td>
						<td />
						<td align='left'><span title="{MapSeed}"><xsl:value-of select="Filename" /></span></td>
					</tr>
					<tr>
						<td align='right'><b>Map Size: </b></td>
						<td />
						<td align='left'>
							<xsl:if test='MapSize = 0'>Normal</xsl:if>
							<xsl:if test='MapSize = 1'>Large</xsl:if>
						</td>
					</tr>
					<tr>
						<td align='right'><b>Played: </b></td>
						<td />
						<td align='left'><xsl:value-of select="Date" /></td>
					</tr>
					<tr>
						<td align='right'><b>Winner(s): </b></td>
						<td />
						<td align='left'><span title="{Winner}">Hover</span></td>
					</tr>
					<xsl:call-template name="Team_Template" />
				</table>
				<br />
				<br />
				<table border='1' width='620px' align="center">
					<tr>
						<td>
							<tt>
								[b]Game Type: [/b] <xsl:call-template name="GetGameType">
														<xsl:with-param name="GameType">
															<xsl:value-of select="GameType" />
														</xsl:with-param>
													</xsl:call-template><br />
								[b]Map: [/b] <xsl:value-of select="Filename" /><br />
								[b]Players: [/b]<br />
								<xsl:call-template name="Team_Template_2" />
								[b]Winner(s): [/b] [spoiler]<xsl:value-of select="Winner" />[/spoiler]
							</tt>
						</td>
					</tr>
				</table>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>