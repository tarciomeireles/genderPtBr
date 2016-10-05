<?php
/**
* This module provides a routine to decide whether a Portuguese/Brazilian name is male
* or female. The algorithm examines a table of suffixes to determine this.
*
* DESCRIPTION
* The table was computed using a recursive space subdivision algorithm operating
* on a database of about 60,000 proper names.
* Typical accuracy is greater than 99%. This makes it useful to find enrollment
* errors in databases.
* This is the PHP version of the Perl script of Marco "Kiko" Carnut <kiko at tempest.com.br>
* http://www.postcogito.org/
*
* PHP version 5
*
* USAGE
*   <?php
*   require 'GenderPtBr.class.php';
*	$gender = new GenderPtBr;
*	
*	echo $gender->analyse('João Da Silva'); // results in 'M'
*	echo $gender->analyse('Maria da Conceição'); // results in 'F'
*	echo $gender->analyse('123456'); // results in 'Unknow'
*	
*	$gender->setGenderNames('Male','Female','Not Applicable');
*	
*	echo $gender->analyse('João Da Silva'); // results in 'Male'
*	echo $gender->analyse('Maria da Conceição'); // results in 'Female'
*	echo $gender->analyse('123456'); // results in 'NotApplicable'
*
*
* @package - GenderPtBr - Decides if a Portuguese proper name is male or female
* @licence  GPL2 - http://www.gnu.org/licenses/gpl.txt
* @author Tarcio Meireles <tarcio.net at gmail.com> http://www.profac.org/tarcio
* @author Marco "Kiko" Carnut <kiko at tempest.com.br> http://www.postcogito.org/
* @license http://www.gnu.org/licenses/gpl.txt
*/


class GenderPtBr{
		
		public $male   = 'M'; 
		public $female = 'F';
		public $unknow = 'Unknow'; 
		
		private $padron = array(
			'a' => array(
				'gender_number' => 0,
				'excep' => array('wilba','rba','vica','milca','meida','randa','uda','rrea','afa','^ha','cha','oha','apha','natha','^elia','rdelia','remia','aja','rja','aka','kka','^ala','gla','tila','vila','cola','orla','nama','yama','inima','jalma','nma','urma','zuma','gna','tanna','pna','moa','jara','tara','guara','beira','veira','kira','uira','pra','jura','mura','tura','asa','assa','ussa','^iata','onata','irata','leta','preta','jota','ista','aua','dua','hua','qua','ava','dva','^iva','silva','ova','rva','wa','naya','ouza')
				),
			'b' => array(
				'gender_number' => 1,
				'excep' => array('inadab')
				),
			'c' => array(
				'gender_number' => 1,
				'excep' => array('lic','tic')
				),
			'd' => array(
				'gender_number' => 1,
				'excep' => array('edad','rid')
				),
			'e' => array(
				'gender_number' => 0,
				'excep' => array('dae','jae','kae','oabe','ube','lace','dece','felice','urice','nce','bruce','dade','bede','^ide','^aide','taide','cide','alide','vide','alde','hilde','asenilde','nde','ode','lee','^ge','ege','oge','rge','uge','phe','bie','elie','llie','nie','je','eke','ike','olke','nke','oke','ske','uke','tale','uale','vale','cle','rdele','gele','tiele','nele','ssele','uele','hle','tabile','lile','rile','delle','ole','yle','ame','aeme','deme','ime','lme','rme','sme','ume','yme','phane','nane','ivane','alvane','elvane','gilvane','ovane','dene','ociene','tiene','gilene','uslene','^rene','vaine','waine','aldine','udine','mine','nine','oine','rtine','vanne','renne','hnne','ionne','cone','done','eone','fone','ecione','alcione','edione','hione','jone','rone','tone','rne','une','ioe','noe','epe','ipe','ope','ppe','ype','sare','bre','dre','bere','dere','fre','aire','hire','ore','rre','tre','dse','ese','geise','wilse','jose','rse','esse','usse','use','aete','waldete','iodete','sdete','aiete','nisete','ezete','nizete','dedite','uite','lte','ante','ente','arte','laerte','herte','ierte','reste','aue','gue','oue','aque','eque','aique','inique','rique','lque','oque','rque','esue','osue','ozue','tave','ive','ove','we','ye','^ze','aze','eze','uze')
				),
			'f' => array(
				'gender_number' => 1,
				),
			'g' => array(
				'gender_number' => 1,
				'excep' => array('eig','heng','mping','bong','jung')
				),
			'h' => array(
				'gender_number' => 1,
				'excep' => array('kah','nah','rah','sh','beth','reth','seth','lizeth','rizeth','^edith','udith','ruth')
				),
			'i' => array(
				'gender_number' => 1,
				'excep' => array('elai','anai','onai','abi','djaci','glaci','maraci','^iraci','diraci','loraci','ildeci','^neci','aici','arici','^elci','nci','oci','uci','kadi','leidi','ridi','hudi','hirlei','sirlei','^mei','rinei','ahi','^ji','iki','isuki','^yuki','gali','rali','ngeli','ieli','keli','leli','neli','seli','ueli','veli','zeli','ili','helli','kelli','arli','wanderli','hami','iemi','oemi','romi','tmi','ssumi','yumi','zumi','bani','iani','irani','sani','tani','luani','^vani','^ivani','ilvani','yani','^eni','ceni','geni','leni','ureni','^oseni','veni','zeni','cini','eini','lini','jenni','moni','uni','mari','veri','hri','aori','ayuri','lsi','rsi','gessi','roti','sti','retti','uetti','aui','iavi','^zi','zazi','suzi')
				),
			'j' => array(
				'gender_number' => 1,
				),
			'k' => array(
				'gender_number' => 1,
				'excep' => array('nak','lk')
				),
			'l' => array(
				'gender_number' => 1,
				'excep' => array('mal','^bel','mabel','rabel','sabel','zabel','achel','thel','quel','gail','lenil','mell','ol')
				),
			'm' => array(
				'gender_number' => 1,
				'excep' => array('liliam','riam','viam','miram','eem','uelem','mem','rem')
				),
			'n' => array(
				'gender_number' => 1,
				'excep' => array('lilian','lillian','marian','irian','yrian','ivian','elan','rilan','usan','nivan','arivan','iryan','uzan','ohen','cken','elen','llen','men','aren','sten','rlein','kelin','velin','smin','rin','istin','rstin','^ann','ynn','haron','kun','sun','yn')
				),
			'o' => array(
				'gender_number' => 1,
				'excep' => array('eicao','eco','mico','tico','^do','^ho','ocio','ako','eko','keiko','seiko','chiko','shiko','akiko','ukiko','miko','riko','tiko','oko','ruko','suko','yuko','izuko','uelo','stano','maurino','orro','jeto','mento','luo')
				),
			'p' => array(
				'gender_number' => 1,
				'excep' => array('yip')
				),
			'r' => array(
				'gender_number' => 1,
				'excep' => array('lar','lamar','zamar','ycimar','idimar','eudimar','olimar','lsimar','lzimar','erismar','edinar','iffer','ifer','ather','sther','esper','^ester','madair','eclair','olair','^nair','glacir','^nadir','ledir','^vanir','^evanir','^cenir','elenir','zenir','ionir','fior','eonor','racyr')
				),
			's' => array(
				'gender_number' => 1,
				'excep' => array('unidas','katias','rces','cedes','oides','aildes','derdes','urdes','leudes','iudes','irges','lkes','geles','elenes','gnes','^ines','aines','^dines','rines','pes','deres','^mires','amires','ores','neves','hais','lais','tais','adis','alis','^elis','ilis','llis','ylis','ldenis','annis','ois','aris','^cris','^iris','miris','siris','doris','yris','isis','rtis','zis','heiros','dys','inys','rys')
				),
			't' => array(
				'gender_number' => 1,
				'excep' => array('bet','ret','^edit','git','est','nett','itt')
				),
			'u' => array(
				'gender_number' => 1,
				'excep' => array('^du','alu','^miharu','^su')
				),
			'v' => array(
				'gender_number' => 1,
				),
			'w' => array(
				'gender_number' => 1,
				),
			'x' => array(
				'gender_number' => 1,
				),
			'y' => array(
				'gender_number' => 1,
				'excep' => array('may','anay','ionay','lacy','^aracy','^iracy','doracy','vacy','aricy','oalcy','ncy','nercy','ucy','lady','hedy','hirley','raney','gy','ahy','rothy','taly','aely','ucely','gely','kely','nely','sely','uely','vely','zely','aily','rily','elly','marly','mony','tamy','iany','irany','sany','uany','lvany','wany','geny','leny','ueny','anny','mary','imery','smery','iry','rory','isy','osy','usy','ty')
				),
			'z' => array(
				'gender_number' => 1,
				'excep' => array('^inez','rinez','derez','liz','riz','uz')
				)
		);
		public function __call($data,$values){			
			
		}
		
		protected function firstName($fullname){
			$names = explode(' ',$fullname);
			return $names[0];
		}
		
		protected function reverseGender($gender){
			if($gender == 1){
				return 0;
			}
			return 1;
		}
		
		public function analyse($fullname){
			$first_name  = strtolower($this->firstName($fullname));
			$last_letter = $this->tiraAcentos(substr($first_name,-1));
			$gender_rule = $this->padron[$last_letter];
			if(isset($gender_rule)){
				if(!isset($gender_rule['excep'])){
					return $this->formatGender($gender_rule['gender_number']);
				}else{
					$excep_test = FALSE;
					foreach($gender_rule['excep'] as $regexp){
						if($excep_test == FALSE){
							$excep_test = eregi($regexp.'$',$first_name);
						}else{
							return $this->formatGender($this->reverseGender($gender_rule['gender_number']));
						}
					}
					return $this->formatGender($gender_rule['gender_number']);
				}
			}else{
				return $this->unknow;
			}
		}
		
		protected function tiraAcentos($str){	
			$no_especial = strtr ($str, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", 
										"SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
			return $no_especial;
		}
		
		public function setGendersNames($maleName = 'M',$femaleName = 'F',$unknowName='Unknow'){
			$this->setMaleName($maleName);
			$this->setMaleName($femaleName);
		}
		
		public function setMaleName($maleName='M'){
			$this->male   = $maleName;
		}
		
		public function setFemaleName($maleName='F'){
			$this->female   = $femaleName;
		}
		
		public function setUnknowName($unknowName='Unknow'){
			$this->unknow   = $unknowName;
		}
		
		protected function formatGender($genderNumber){
			if($genderNumber == 0){
				return $this->female;
			}elseif($genderNumber == 1){
				return $this->male;
			}	
		}
}