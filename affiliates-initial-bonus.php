<?php
/**
 * Plugin Name: Affiliates Referral Bonus
 * Plugin URI: http://www.netpad.gr
 * Description: Grant your affiliates a bonus upon first referral
 * Version: 1.0
 * Author: George Tsiokos
 * Author URI: http://www.netpad.gr
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright (c) 2015-2016 "gtsiokos" George Tsiokos www.netpad.gr
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

const BONUS_RATE = 0.12;
const BONUS_AMOUNT = 10; // The bonus amount granted to the respective affiliate

add_action( 'admin_notices', 'aib_check_dependencies' );

function aib_check_dependencies () {
	$active_plugins = get_option( 'active_plugins', array() );
	$affiliates_is_active = in_array( 'affiliates/affiliates.php', $active_plugins ) || in_array( 'affiliates-pro/affiliates-pro.php', $active_plugins ) || in_array( 'affiliates-enterprise/affiliates-enterprise.php', $active_plugins );
	$woocommerce_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
	
	if ( !$affiliates_is_active ) {
		echo "<div class='error'><strong>Affiliates Initial Bonus</strong> plugin requires one of the <a href='http://wordpress.org/plugins/affiliates/'>Affiliates</a>, <a href='http://www.itthinx.com/shop/affiliates-pro/'>Affiliates Pro</a> or <a href='http://www.itthinx.com/shop/affiliates-enterprise/'>Affiliates Enterprise</a> plugins to be installed and activated.</div>";
	}

	if ( !$woocommerce_is_active ) { 
		echo "<div class='error'><strong>Affiliates Initial Bonus</strong> plugin requires <a href='http://wordpress.org/plugins/woocommerce/'>woocommerce</a> plugin to be installed and activated.</div>";
	}
}

add_action( 'affiliates_referral', 'affiliates_referral_initial_bonus', 10, 2 );

function affiliates_referral_initial_bonus( $referral_id, $params ) {

	$post_id = $params['post_id'];
	$description = "Referral Bonus";
	$bonus_rate = BONUS_RATE;
	$bonus_amount = BONUS_AMOUNT;
	$currency_id = get_option( 'woocommerce_currency' );
	$aff_default_referral_status = get_option( 'aff_default_referral_status' ) ? get_option( 'aff_default_referral_status' ) : "pending";
	$type = "initial bonus for " . $referral_id;
	$reference = "initial bonus for " . $referral_id;
	$data = null;
	$aff_id = $params['affiliate_id'];
	$total_referrals = affiliates_get_affiliate_referrals( $aff_id, $from_date = null , $thru_date = null, $status = $aff_default_referral_status, $precise = false );
	
	if ( $total_referrals < 2 ) {
		if ( isset( $params['base_amount'] ) ) {
			$amount = bcmul( $bonus_rate, $params['base_amount'], 2 );
		} else {
			$amount = bcmul( $bonus_amount, 1, 2 );
		}
	
		affiliates_add_referral( $aff_id, $post_id, $description, $data, $amount, $currency_id, $aff_default_referral_status, $type, $reference );
	} else {
		return;
	}

}

?>
