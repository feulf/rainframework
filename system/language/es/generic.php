<?php
	
	// locale
	define( "LOCALE", "es_MX.utf-8,es_MX,esp,Espa&ntilde;ol" );		//define locale language

  $days = array("Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado","Domingo");//needed to use %l option for date()
  $abbrDays = array("Lun","Mar","Mi&eacute;","Jue","Vie","S&aacute;b","Dom");//needed to use %D option for date()
  
	//Time
	define( "DATE_FORMAT"		, "%d %b %Y" );
	define( "DATE_TIME_FORMAT"	, $abbrDays[ date("%w") ] . " - %I:%M %p" );//array notation to simulate %D
	define( "TIME_FORMAT"		, "%I:%M %p" );
	define( "MONTH_FORMAT"		, "%d %b" );
	
	//Time string
	define("_SECOND_"			, "segundo" );
	define("_SECONDS_"			, "segundos" );
	define("_MINUTE_"			, "minuto" );
	define("_MINUTES_"			, "minutos" );
	define("_HOUR_"				, "hora" );
	define("_HOURS_"			, "horas" );
	define("_DAY_"				, "dia" );
	define("_DAYS_"				, "dias" );
	define( "_TODAY_"			, "hoy" );
	define( "_YESTERDAY_"		, "ayer" );
	define( "_TOMORROW_"		, "ma&ntilde;ana" );
	define( "_SECONDS_AGO_"		, "segundos atr&aacute;s" );	
	define( "_MINUTES_AGO_"		, "minutos atr&aacute;s" );	
	define( "_HOURS_AGO_"		, "horas atr&aacute;s" );	
	define( "_DAYS_AGO_"		, "dias atr&aacute;s" );

	//Money
	define( "DEC_POINT"			, "." );
	define( "THOUSANDS_SEP"		, "," );
	define( "CURRENCY" 			, "$" );
	define( "CURRENCY_SIDE" 	, "0" ); //0 => left , 1 => right
	
	//Generic
	define("_YES_"				, "Si");
	define("_NO_"				, "No");
	define("_ENABLED_"			,"Habilitado");	
	define("_DISABLED_"			,"Inhabilitado");	
	define("_TRUE_"				,"Verdadero");
	define("_FALSE_"			,"Falso");	

?>
