<?php
require_once("xmlobj.php");
require_once("xmlstream.php");
require_once("logging.php");

class XMPP extends XMLStream {
	var $server;
	var $user;
	var $password;
	var $resource;
	var $fulljid;
	var $ver_from;
	var $ver_type;
	var $ver_to;
	var $ver_chat_type;
	var $tconf;
	var $start1;
	var $user_name_inconf;
	var $r1;
    var $role_st = array();


    private $name;


	function XMPP($host, $port, $user, $password, $resource, $server=Null, $printlog=False, $loglevel=Null) {
		$this->XMLStream($host, $port, $printlog, $loglevel);
		$this->user = $user;
		$this->password = $password;
		$this->resource = $resource;
		if(!$server) $server = $host;
		$this->stream_start = '<stream:stream to="' . $server . '" xmlns:stream="http://etherx.jabber.org/streams" xmlns="jabber:client" version="1.0">';
		$this->stream_end = '</stream:stream>';
		$this->addHandler('features', 'http://etherx.jabber.org/streams', 'features_handler');
		$this->addHandler('success', 'urn:ietf:params:xml:ns:xmpp-sasl', 'sasl_success_handler');
		$this->addHandler('failure', 'urn:ietf:params:xml:ns:xmpp-sasl', 'sasl_failure_handler');
		$this->addHandler('proceed', 'urn:ietf:params:xml:ns:xmpp-tls', 'tls_proceed_handler');
		$this->default_ns = 'jabber:client';
		$this->addHandler('message', 'jabber:client', 'message_handler');
		$this->addHandler('presence', 'jabber:client', 'presence_handler');
		$this->use_encryption = false;

	}

	function message_handler($xml) {
		$payload['type'] = $xml->attrs['type'];
		//if(!$paytload['type']) $payload['type'] = 'chat';
		if($paytload['type']=='error') $payload['type'] = 'groupchat';
		$payload['from'] = $xml->attrs['from'];
		$payload['body'] = $xml->sub('body')->data;
		$this->log->log("Message: {$xml->sub('body')->data}", LOGGING_DEBUG);
		$this->event('message', $payload);
	}

	function message($to, $body, $type='chat', $subject=Null) {
		$to = htmlspecialchars($to);
		$body = htmlspecialchars($body);
		$subject = htmlspecialchars($subject);
		$out = "<message from='{$this->fulljid}' to='$to' type='$type'>";
		if($subject) $out .= "<subject>$subject</subject>";
		$out .= "<body>$body</body></message>";
		$this->send($out);
	}

function presence($status=Null, $show='available', $to=Null) {
		$to = htmlspecialchars($to);
		$status = htmlspecialchars($status);
		if($show == 'unavailable') $type = 'unavailable';
		$out = "<presence";
		if($to) $out .= " to='$to'";
		if($type) $out .= " type='$type'";
		if($show == 'available' and !$status) {
			$out .= "/>";
		} else {
			$out .= ">";
			if($status) $out .= "<status>$status</status>";
			if($show != 'available') $out .= "<show>$show</show>";
			$out .= "</presence>";
		}
		$this->send($out);
	}

public function presence_handler($xml) {
		$payload['type'] = $xml->attrs['type'];
		if(!$payload['type']) $payload['type'] = 'available';
		$payload['show'] = $xml->sub('show')->data;
		if(!$payload['show']) $payload['show'] = $payload['type'];
		$payload['from'] = $xml->attrs['from'];
		$payload['status'] = $xml->sub('status')->data;

		$tmp = print_r($xml, true);
            $tmp = str_replace ("\n", '', $tmp);
			$tmp = str_replace ("\r", '', $tmp);
			$pattern = '/\[role\] => (moderator|participant)/i';
			preg_match($pattern, $tmp, $matches);
			$payload['role'] = $matches[1];
         	$pattern = "/\[jid\] => ((.*)\/(.*))\[affiliation/i";
			preg_match($pattern, $tmp, $matches);
			$payload['jid'] = trim($matches[1]);


   		$found = FALSE;
   		foreach ($this->role_st as $key => $val) {

	   		if ($payload['from'] == $val)
	   		{
		   		$found = TRUE;
		   		$key = $key;
		   		break;
	   		}

   		}

   		if ($found)
		{
		if (empty ($payload['jid'])) $payload['jid'] = 'none';
		if (empty ($payload['role'])) $payload['jid'] = 'none';
		$this->role_st[$key] = array($payload['from'], $payload['jid'], $payload['role']);
		}
		else
		{
		if (empty ($payload['jid'])) $payload['jid'] = 'none';
		if (empty ($payload['role'])) $payload['jid'] = 'none';
		array_push ($this->role_st, array($payload['from'], $payload['jid'], $payload['role']));
		}

		$this->r1 = $this->role_st;
   	    $this->event('presence', $payload);

	}


function show ($show='available', $to=Null) {
		$to = htmlspecialchars($to);
		$status = htmlspecialchars($status);
		if($show == 'unavailable') $type = 'unavailable';
		$out = "<presence";
		if($to) $out .= " to='$to'";
		if($type) $out .= " type='$type'";
		$out .= ">";
		$out .= '<show>'.$show.'</show>';
		$out .= "</presence>";
		$this->send($out);
	}


    function topic2 ($to,$txt='Feel free to change me!') {
		$to = htmlspecialchars($to);
		$out = '<message type="groupchat" id="'.$id.'" to="'.$to.'"><subject>'.$txt.'</subject><body>/me РёР·РјРµРЅРёР» С‚РµРјСѓ РЅР°: '.$txt.'</body></message>';
		$this->send($out);
	}


    function joinc($to,$txt='Online') {
		$to = htmlspecialchars($to);
		$out = '<presence to="'.$to.'" ><status>'.$txt.'</status><x xmlns="http://jabber.org/protocol/muc"><history maxchars="0" maxstanzas="0"/></x></presence>';
		$this->send($out);
	}

//MUC UNITS

		//KICK
		function kick($to, $nick,$reason) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" role="none"><reason>'.$reason.'</reason></item></query></iq>';
				$this->send($out);
			}

		//BAN
		function ban($to, $jid,$reason) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item jid="'.$jid.'" affiliation="outcast"><reason>'.$reason.'</reason></item></query></iq>';
				$this->send($out);
			}

		//VISITOR
		function visitor($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" role="visitor"></item></query></iq>';
				$this->send($out);
			}

		//participant
		function participant($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" role="participant"></item></query></iq>';
				$this->send($out);
			}


		//member
		function member($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" affiliation="member"></item></query></iq>';
				$this->send($out);
			}

		//moderator
		function moderator($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" role="moderator"></item></query></iq>';
				$this->send($out);
			}

		//admin
		function admin($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" affiliation="admin"></item></query></iq>';
				$this->send($out);
			}

		//owner
		function owner($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" affiliation="owner"></item></query></iq>';
				$this->send($out);
			}

		//none
		function none($to, $nick) {
				$out = '<iq type="set" to="'.$to.'"><query xmlns="http://jabber.org/protocol/muc#admin"><item nick="'.$nick.'" affiliation="none"></item></query></iq>';
				$this->send($out);
			}


		function confquit($to) {
				$to = htmlspecialchars($to);
				$out = '<presence type="unavailable" to="'.$to.'" />';
				$this->send($out);
			}

		public function vinfo($me, $from, $msgtype,$what,$ansver_to_conf,$user_in_conf) {
				$me = htmlspecialchars($me);
				$from = htmlspecialchars($from);
				$id = $this->getId();
				$this->ver_from = $me;
				$this->ver_to = $from;
		        $this->ver_chat_type = $msgtype;
		        $this->ver_type = $what;
		        $this->tconf = $ansver_to_conf;
		        $this->user_name_inconf = $user_in_conf;

		      	//start ping timer
		   		if ($what == 'ping')
				{
				//ping timer
				$start1=gettimeofday();
				$this->start1=$start1;
				for($i=0; $i<10000; $i++) { // do nothing
				}

				}

				$out = '<iq from="'.$me.'" to="'.$from.'" id="'.$id.'" type="get"><query xmlns="jabber:iq:version"/></iq>';
				$this->send($out);

			}

		function features_handler($xml) {
			if($xml->hassub('starttls') and $this->use_encryption) {
				$this->send("<starttls xmlns='urn:ietf:params:xml:ns:xmpp-tls'><required /></starttls>");
			} elseif($xml->hassub('bind')) {
				$id = $this->getId();
				$this->addIdHandler($id, 'resource_bind_handler');
				$this->send("<iq xmlns=\"jabber:client\" type=\"set\" id=\"$id\"><bind xmlns=\"urn:ietf:params:xml:ns:xmpp-bind\"><resource>{$this->resource}</resource></bind></iq>");
			} else {
				$this->log->log("Attempting Auth...");
				$this->send("<auth xmlns='urn:ietf:params:xml:ns:xmpp-sasl' mechanism='PLAIN'>" . base64_encode("\x00" . $this->user . "\x00" . $this->password) . "</auth>");
			}
		}

		function sasl_success_handler($xml) {
			$this->log->log("Auth success!");
			$this->reset();
		}

		function sasl_failure_handler($xml) {
			$this->log->log("Auth failed!", LOGGING_ERROR);
			$this->disconnect();
		}

		function resource_bind_handler($xml) {
			if($xml->attrs['type'] == 'result') {
				$this->log->log("Bound to " . $xml->sub('bind')->sub('jid')->data);
				$this->fulljid = $xml->sub('bind')->sub('jid')->data;
			}
			$id = $this->getId();
			$this->addIdHandler($id, 'session_start_handler');
			$this->send("<iq xmlns='jabber:client' type='set' id='$id'><session xmlns='urn:ietf:params:xml:ns:xmpp-session' /></iq>");
		}

		function session_start_handler($xml) {
			$this->log->log("Session started");
			$this->event('session_start');
		}

		function tls_proceed_handler($xml) {
			$this->log->log("Starting TLS encryption");
			stream_socket_enable_crypto($this->socket, True, STREAM_CRYPTO_METHOD_TLS_CLIENT);
			$this->reset();
		}


		public function income_iq($xml)
		{
			//version
			if ($this->ver_type == 'ver')
			{
			$this->ver_type = 'none';
			$tmp['type'] = $xml->attrs['type'];

				if($xml->hassub('query'))
				{				$tmp['name'] = $xml->sub(0)->sub('name')->data;
				$tmp['version'] = $xml->sub(0)->sub('version')->data;
				$tmp['os'] = $xml->sub(0)->sub('os')->data;

				//$outvalue = print_r ($tmp, true);
			    //$this->message ('narkoz@jabber.ru', $outvalue, 'chat');

				if ($this->ver_chat_type == 'chat') $this->message ($this->ver_to, $tmp['name'].$tmp['version'].' '.$tmp['os'], $this->ver_chat_type);

				if ($this->ver_chat_type == 'groupchat')
					{					if (empty ($tmp['name'])) $tmp['name']='Not found!';
					$this->message ($this->tconf, $this->user_name_inconf.': '.$tmp['name'].$tmp['version'].' '.$tmp['os'], $this->ver_chat_type);
					}				}
			}

			//ping
			if ($this->ver_type == 'ping')
			{
			$end1=gettimeofday();
			$totaltime1 = (float)($end1['sec'] - $this->start1['sec']) + ((float)($end1['usec'] - $this->start1['usec'])/1000000);
			if ($this->ver_chat_type == 'chat') $this->message ($this->ver_to, 'РџРѕРЅРі: '.substr ($totaltime1, 0,4).' СЃРµРє.', $this->ver_chat_type);
			if ($this->ver_chat_type == 'groupchat') $this->message ($this->tconf, $this->user_name_inconf.': РџРѕРЅРі: '.substr ($totaltime1, 0,4).' СЃРµРє.', $this->ver_chat_type);
			$this->ver_type = 'none';
			}
		}

}
?>
