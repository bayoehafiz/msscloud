<?php
/**
 * @package Admin
 * @todo    Settings page for WHMpress admin panel
 */

if ( ! defined( 'WHMP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

global $wpdb;
$countries  = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}whmpress_countries` ORDER BY `country_name`" );
$WHMP       = new WHMPress;
$currencies = $WHMP->get_currencies();

$newTR = "<tr>";
$newTR .= '<td><select name="whmp_countries_currencies[country][]">';
$newTR .= '<option value="">' . __( '-- Select Country --', 'vhost' ) . '</option>';
foreach ( $countries as $country ):
	$newTR .= '<option value="' . $country->country_code . '">' . $country->country_name . '</option>';
endforeach;
$newTR .= '</select>';
$newTR .= '</td>';
$newTR .= '<td>';
$newTR .= '<select name="whmp_countries_currencies[currency][]">';
$newTR .= '<option value="">' . _e( '-- Currency --', 'whmpress' ) . '</option>';
foreach ( $currencies as $currency ):
	$newTR .= '<option value="' . $currency["id"] . '">' . $currency["code"] . '</option>';
endforeach;
$newTR .= '</select> ';
$newTR .= '[<a title="Remove this country" href="javascript:;" onclick="Remove(this)">X</a>]';
$newTR .= '</td>';
$newTR .= '</tr>';
$newTR = str_replace( '"', "'", $newTR );
?>
<style>
	table thead td {
		font-weight: bold;
		font-size: 16px;
		padding: 5px;
	}
</style>
<div class="wrap">
	<h2><?php _e( "Country Redirection", "whmpress" ) ?></h2>
	<p>
		<?php _e( "Set default currency for specific country. Default currency is", "whmpress" ) ?>
		<?php $default_curr = whmp_get_default_currency_id(); ?>
		<select onchange="ChangeDefaultCurrency(this)">
			<?php
			foreach ( $currencies as $curr ): ?>
				<option <?php if ( $default_curr == $curr["id"] )
					echo "selected=selected" ?> value="<?php echo $curr["id"] ?>"><?php echo $curr["code"] ?></option>
			<?php endforeach; ?>
		</select> <span style="display: none;" id="saving_span"><?php _e( "Saving...", "whmpress" ) ?></span>
	</p>
	<form method="post" action="options.php">
		<?php settings_fields( 'whmp_countries' );
		do_settings_sections( 'whmp_countries' ); ?>
		<table id="country_table">
			<thead>
			<tr>
				<td colspan="2">
					<button onclick="AddTR()" type="button" class="button button-primary">Add</button>
				</td>
			</tr>
			<tr>
				<td>Country</td>
				<td>Currency</td>
			</tr>
			</thead>
			<tbody>
			<?php
			$whmp_countries_currencies = get_option( "whmp_countries_currencies" );
			if ( ! is_array( $whmp_countries_currencies ) ) {
				$whmp_countries_currencies = [];
			}
			for ( $x = 0; $x < count( $whmp_countries_currencies["country"] ); $x ++ ): ?>
				<tr>
					<td>
						<select name="whmp_countries_currencies[country][]">
							<option value="">-- Select Country --</option>
							<?php foreach ( $countries as $country ): $S = $whmp_countries_currencies["country"][ $x ] == $country->country_code ? "selected=selected" : ""; ?>
								<option <?php echo $S ?>
									value="<?php echo $country->country_code ?>"><?php echo $country->country_name ?></option>
							<?php endforeach ?>
						</select>
					</td>
					<td>
						<select name="whmp_countries_currencies[currency][]">
							<option value="">-- Currency --</option>
							<?php foreach ( $currencies as $currency ): $S = $whmp_countries_currencies["currency"][ $x ] == $currency["id"] ? "selected=selected" : ""; ?>
								<option <?php echo $S ?>
									value="<?php echo $currency["id"] ?>"><?php echo $currency["code"] ?></option>
							<?php endforeach ?>
						</select>
						[<a title="Remove this country" href="javascript:;" onclick="Remove(this)">X</a>]
					</td>
				</tr>
			<?php endfor; ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="2">
					<button class="button button-primary">Save Countries</button>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
</div>

<script>
	function AddTR ()
	{
		//alert ( jQuery("#new_tr").html() );
		jQuery("#country_table tbody").append("<?php echo $newTR ?>");
	}
	function Remove (tthis)
	{
		jQuery(tthis).parent().parent().remove();
	}
	function ChangeDefaultCurrency (tthis)
	{
		jQuery("#saving_span").show("slow");
		jQuery.post("<?php echo WHMP_PLUGIN_URL ?>/includes/ajax.php?set_default_currency", {new_curr: jQuery(tthis).val()}, function (data)
		{
			if (data == "OK") window.location.reload();
			else alert("Error: " + data);
		});
	}
</script>