<?php
if (!$bInSite) die();
//=========================================================
// site labels used in Public Interface
//=========================================================
    $aLabel[LANG_EN]['strSiteTitle'] = 'Programata + TV';
    $aLabel[LANG_EN]['strMoto'] = 'free cultural guide';
    $aLabel[LANG_EN]['strSiteDescription'] = 'Programata is a daily updated program of the cultural events in Sofia, Varna, Bourgas, Plovdiv and Stara Zagora: movies, performances, exhibitions, festivals, modern and classical music, concerts, parties, as well as detailed info about clubs, bars, restaurants, etc.';
    $aLabel[LANG_EN]['strSiteKeywords'] = 'cinema, movies, video, theatre, exhibitions, museums, classic concerts, culture, festival, live music, clubs, bars, restaurants';
    $aLabel[LANG_EN]['strCopy'] = 'Programata Media Group. All rights reserved.';
//=========================================================
// entities used in pages of Admin Interface
//=========================================================
    $aTemplate[LANG_EN][ENT_HOME] = 'Home';
    $aTemplate[LANG_EN][ENT_USER] = 'Users';
    $aTemplate[LANG_EN][ENT_PAGE] = 'Pages';
    //$aTemplate[LANG_EN][ENT_LOOKUP] = 'Categories';
    $aTemplate[LANG_EN][ENT_NEWS] = 'News';
    $aTemplate[LANG_EN][ENT_PUBLICATION] = 'Interviews';
    $aTemplate[LANG_EN][ENT_FESTIVAL] = 'Festivals';
	$aTemplate[LANG_EN][ENT_URBAN] = 'Urban';
	$aTemplate[LANG_EN][ENT_MULTY] = 'Jacobs + The Straw';
	$aTemplate[LANG_EN][ENT_EXTRA] = 'Extra';
    $aTemplate[LANG_EN][ENT_COMMENT] = 'Comments';
    $aTemplate[LANG_EN][ENT_LABEL] = 'Categories';
    $aTemplate[LANG_EN][ENT_PLACE] = 'Places';
    $aTemplate[LANG_EN][ENT_EVENT] = 'Events';
    $aTemplate[LANG_EN][ENT_PROMOTION] = 'Promotions';
    //$aTemplate[LANG_EN][ENT_JOB] = 'Jobs';
    $aTemplate[LANG_EN][ENT_PROGRAM] = 'Programme';
    $aLabel[LANG_EN]['strEntPlaceNote'] = ' (Where)';
    $aLabel[LANG_EN]['strEntEventNote'] = ' (What)';
    $aLabel[LANG_EN]['strEntProgramNote'] = ' (What, where, when)';
//=========================================================
// related entities used in pages of Admin Interface
//=========================================================
    $aRelatedTemplate[LANG_EN][ENT_ATTACHMENT] = 'Attachments';
    $aRelatedTemplate[LANG_EN][ENT_LINK] = 'Links';
    $aRelatedTemplate[LANG_EN][ENT_EMAIL] = 'E-mails';
    $aRelatedTemplate[LANG_EN][ENT_ADDRESS] = 'Addresses';
    $aRelatedTemplate[LANG_EN][ENT_PHONE] = 'Phones';
    $aRelatedTemplate[LANG_EN][ENT_PLACE_HALL] = 'Halls';
    $aRelatedTemplate[LANG_EN][ENT_DATE_PERIOD] = 'Periods';
    $aRelatedTemplate[LANG_EN][ENT_DATE_TIME] = 'Date-Time';
    $aRelatedTemplate[LANG_EN][ENT_PLACE_GUIDE] = 'Place guide';
//=========================================================
// order statuses
//=========================================================
    $aLabel[LANG_EN]['aUserStatus'] = array(USER_GUEST=>'Inactive user',
                                            USER_REGULAR=>'Registered user',
                                            USER_ADMIN=>'Administrator');
//=========================================================
// intro labels used in Admin Interface
//=========================================================
    $aLabel[LANG_EN]['strAdmin'] = 'Administration';
    $aLabel[LANG_EN]['strAdminManual'] = 'Administration manual';
    $aLabel[LANG_EN]['strAdminTitle'] = 'Programata.bg administration';
    $aLabel[LANG_EN]['strAdminWelcome'] = 'Welcome to the administration interface of Programata.bg website. Please select an item from the menu to continue.';
//=========================================================
// Admin Login labels
//=========================================================
    $aLabel[LANG_EN]['strUsername'] = 'Username';
    $aLabel[LANG_EN]['strPassword'] = 'Password';
    $aLabel[LANG_EN]['strOldPassword'] = 'Old password';
    $aLabel[LANG_EN]['strNewPassword'] = 'New password';
    $aLabel[LANG_EN]['strNewPassword2'] = 'Confirm password';
    $aLabel[LANG_EN]['strMatchFailed'] = 'Passwords do not match.';
    $aLabel[LANG_EN]['strLogin'] = 'login';
    $aLabel[LANG_EN]['strDoLogin'] = 'login';
    $aLabel[LANG_EN]['strLogout'] = 'logout';
    $aLabel[LANG_EN]['strDoLogout'] = 'logout';
    $aLabel[LANG_EN]['strQuestions'] = 'Questions';
    $aLabel[LANG_EN]['strWhyToRegister'] = 'Why to register?';
    $aLabel[LANG_EN]['strRememberMe'] = 'Remember me'; // on this computer
    $aLabel[LANG_EN]['strInvalid'] = 'Invalid username and/or password, or inactivated password. Please try again.';
    $aLabel[LANG_EN]['strLogoutOK'] = 'You have successfully logged out.';
    $aLabel[LANG_EN]['strLoginOK'] = 'You have successfully logged in.';
    $aLabel[LANG_EN]['strLoginIntro'] = 'Please enter your username and password.';
    $aLabel[LANG_EN]['strNewsletters'] = 'Newsletters';
    $aLabel[LANG_EN]['aNewsletterCategories'] = array(1=>'Classic',
                                                      2=>'Family &amp; Entertainment',
                                                      3=>'Folk',
                                                      4=>'Highlights from all different categories',
                                                      5=>'Music',
                                                      6=>'&Ouml;T Special Offers',
                                                      7=>'Sports',
                                                      8=>'Theatre &amp; Cabaret');
//=========================================================
// site labels used in forms
//=========================================================
    $aLabel[LANG_EN]['strRequired'] = 'Please fill in all fields marked with asterisks ('.formatVal().').';
    $aLabel[LANG_EN]['strAnyRequired'] = 'Please fill in at least one of the fields marked with asterisks ('.formatVal().').';
    $aLabel[LANG_EN]['strSendOK'] = 'Your message has been sent by e-mail.';
    $aLabel[LANG_EN]['strSendFailed'] = 'Your message has not been sent. Please <a href="#"  onclick="history.back();return false;">go back</a> and try again.';
    $aLabel[LANG_EN]['strSend'] = 'Send';
    $aLabel[LANG_EN]['strRetrieveOK'] = 'Matching account data has been found.';
    $aLabel[LANG_EN]['strRetrieveFailed'] = 'Matching account data has not been found. Please <a href="#"  onclick="history.back();return false;">go back</a> and try again.';
    $aLabel[LANG_EN]['strFind'] = 'Find';
    $aLabel[LANG_EN]['strUserDataOK'] = 'A message with login details has been sent to your e-mail.';
    $aLabel[LANG_EN]['strUserDataFailed'] = 'A message with login details has not been sent.';
    $aLabel[LANG_EN]['strUserLogin'] = 'You can continue as a registered user by logging in the website.';
    $aLabel[LANG_EN]['strUsernameTaken'] = 'The username you have entered is already taken. Please <a href="#"  onclick="history.back();return false;">go back</a> and try again.';
    $aLabel[LANG_EN]['strRegister'] = 'Register';
    $aLabel[LANG_EN]['strUserWelcome'] = 'Hello <strong>%name</strong>!<br /><br />After successfully logging in the website, you may continue using all advantages available for registered users.';
    $aLabel[LANG_EN]['strSaveOK'] = 'The data you have entered has been saved.';
    $aLabel[LANG_EN]['strSaveFailed'] = '<span class="err">The data you have entered has not been saved.</span> Please <a href="#"  onclick="history.back();return false;">go back</a> and try again.';
    $aLabel[LANG_EN]['strSave'] = 'Save';
    $aLabel[LANG_EN]['strReload'] = 'Reload';
//=========================================================
// Registration
//=========================================================
    $aLabel[LANG_EN]['strRegistrationTitle'] = 'Welcome to Programata.bg';
    $aLabel[LANG_EN]['strRegistrationMessage'] = 'Hello, %name,<br />
Thank you for registering in Programata.bg.<br />
<br />
Your username is: %user<br />
Your password is: %pass<br />
<br />
In order to activate your password, please visit the following link (or copy and paste it into the address bar of your browser):<br />
%link<br />
<br />
Programata.bg team.<br />
<a href="http://'.SITE_URL.'">'.SITE_URL.'</a>.<br />';
//=========================================================
// Reminder
//=========================================================
    $aLabel[LANG_EN]['strReminderMessage'] = 'Hello, %name,<br />
Your personal calendar reminds that you have plans for today.<br />
You can visit us at <a href="http://'.SITE_URL.'">'.SITE_URL.'</a>.
<br />
Programata.bg team.<br />';
//=========================================================
    $aLabel[LANG_EN]['strFirstName'] = 'First name';
    $aLabel[LANG_EN]['strLastName'] = 'Last name';
    $aLabel[LANG_EN]['strFullName'] = 'Your name';
    $aLabel[LANG_EN]['strProfession'] = 'Profession';
    $aLabel[LANG_EN]['strInterests'] = 'Interests';
    $aLabel[LANG_EN]['strSex'] = 'Sex';
    $aLabel[LANG_EN]['aSex'] = array(1=>'Male', 2=>'Female');
    $aLabel[LANG_EN]['strAge'] = 'Age';
    $aLabel[LANG_EN]['strBirthday'] = 'Date of birth';
    $aLabel[LANG_EN]['strCompany'] = 'Company';
    $aLabel[LANG_EN]['strCompanyActivity'] = 'Company activity';
    $aLabel[LANG_EN]['strPosition'] = 'Position';
    $aLabel[LANG_EN]['strAdType'] = 'Advertisement type';
    $aLabel[LANG_EN]['aAdTypes'] = array(1=>'online, in Programata website',
                                         2=>'offline, in the print magazines',
                                         3=>'in both editions');
    $aLabel[LANG_EN]['strAdDescription'] = 'Brief description of your advertising ideas';
    $aLabel[LANG_EN]['strPhone'] = 'Phone';
    $aLabel[LANG_EN]['strEmail'] = 'E-mail';
    $aLabel[LANG_EN]['strMessage'] = 'Recommendations, questions and comments';//'Message';
    $aLabel[LANG_EN]['strAddress'] = 'Address';
    $aLabel[LANG_EN]['strCity'] = 'City';
    $aLabel[LANG_EN]['strCountry'] = 'Country';
    $aLabel[LANG_EN]['strDownload'] = 'Useful Information';
    //$aLabel[LANG_EN]['strProgramataTGI'] = 'Programata Target Group Index 2004';
    //$aLabel[LANG_EN]['strReadersProfile'] = 'Readers Profile 2004';
    //$aLabel[LANG_EN]['strPricelistOffline'] = 'Pricelist Print Magazines';
    //$aLabel[LANG_EN]['strPricelistOnline'] = 'Pricelist Website';
    $aLabel[LANG_EN]['strStats'] = 'Website statistics';
    $aLabel[LANG_EN]['strFriendName'] = 'Recipient name';
    $aLabel[LANG_EN]['strFriendEmail'] = 'Recipient e-mail';
    $aLabel[LANG_EN]['strDefaultDate'] = ' <span class="note">(DD.MM.YYYY)</span>';
    $aLabel[LANG_EN]['strDefaultTime'] = '08:00 - 18:00';
    $aLabel[LANG_EN]['aMonths'] = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
    $aLabel[LANG_EN]['aMonthsShort'] = array(1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec');
    $aLabel[LANG_EN]['aDays'] = array(1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday');
    $aLabel[LANG_EN]['aDaysShort'] = array(1=>'Mon', 2=>'Tue', 3=>'Wed', 4=>'Thu', 5=>'Fri', 6=>'Sat', 7=>'Sun');
    $aLabel[LANG_EN]['aAlphabet'] = array(
            0=>'All', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'0',
            11=>'a', 12=>'b', 13=>'c', 14=>'d', 15=>'e', 16=>'f', 17=>'g', 18=>'h', 19=>'i', 20=>'j',
            21=>'k', 22=>'l', 23=>'m', 24=>'n', 25=>'o', 26=>'p', 27=>'q', 28=>'r', 29=>'s', 30=>'t',
            31=>'u', 32=>'v', 33=>'w', 34=>'x', 35=>'y', 36=>'z');
    $aLabel[LANG_EN]['aAlphabetGroups'] = array(1=>'0-9', 2=>'A-H', 3=>'I-N', 4=>'O-T', 5=>'U-Z');
    $aLabel[LANG_EN]['aAlphabetGroupsLetters'] = array(
		1=>array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'),
		2=>array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'),
		3=>array('i', 'j', 'k', 'l', 'm', 'n'),
		4=>array('o', 'p', 'q', 'r', 's', 't'),
		5=>array('u', 'v', 'w', 'x', 'y', 'z')
    );
    $aLabel[LANG_EN]['aAlphabetLettersKeys'] = array(
		'1'=>1, '2'=>1, '3'=>1, '4'=>1, '5'=>1, '6'=>1, '7'=>1, '8'=>1, '9'=>1, '0'=>1,
		'a'=>2, 'b'=>2, 'c'=>2, 'd'=>2, 'e'=>2, 'f'=>2, 'g'=>2, 'h'=>2,
                'A'=>2, 'B'=>2, 'C'=>2, 'D'=>2, 'E'=>2, 'F'=>2, 'G'=>2, 'H'=>2,
		'i'=>3, 'j'=>3, 'k'=>3, 'l'=>3, 'm'=>3, 'n'=>3,
                'I'=>3, 'J'=>3, 'K'=>3, 'L'=>3, 'M'=>3, 'N'=>3,
		'o'=>4, 'p'=>4, 'q'=>4, 'r'=>4, 's'=>4, 't'=>4,
                'O'=>4, 'P'=>4, 'Q'=>4, 'R'=>4, 'S'=>4, 'T'=>4,
		'u'=>5, 'v'=>5, 'w'=>5, 'x'=>5, 'y'=>5, 'z'=>5,
                'U'=>5, 'V'=>5, 'W'=>5, 'X'=>5, 'Y'=>5, 'Z'=>5);
    $aLabel[LANG_EN]['strUpper'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $aLabel[LANG_EN]['strLower'] = 'abcdefghijklmnopqrstuvwxyz';
    $aLabel[LANG_EN]['aCities'] = array(1=>'Sofia', 2=>'Plovdiv', 3=>'Varna', 4=>'Bourgas', 14=>'Stara Zagora');
    $aLabel[LANG_EN]['aCitiesAll'] = array(1=>'Sofia', 2=>'Plovdiv', 3=>'Varna', 4=>'Bourgas', 5=>'Nessebar',
                                           6=>'Sozopol', 7=>'Albena', 8=>'Sunny Beach', 9=>'Balchik', 10=>'Golden Sands',
                                           11=>'Primorsko', 12=>'Kiten', 13=>'Tsarevo', 14=>'Stara Zagora', 15=>'Pomorie',
                                           16=>'Kranevo', 17=>'Kavarna', 18=>'Byala', 19=>'Obzor', 20=>'Lozenets', 21=>'Chernomorets',
                                           22=>'St. St. Konstantin &amp; Elena', 23=>'Brestovitsa', 24=>'Starozagorcki bani', 25=>'Shabla', 26=>'St. Vlas');
    $aLabel[LANG_EN]['strThisWeek'] = 'Entire week';
    $aLabel[LANG_EN]['strAnyTime'] = 'Any time';
    $aLabel[LANG_EN]['strWhichCity'] = 'In city';
    $aLabel[LANG_EN]['strWhichSection'] = 'In section';
//=========================================================
    $aLabel[LANG_EN]['strRSS'] = 'Feeds (RSS 2.0)';
    $aLabel[LANG_EN]['strAdvertisement'] = 'Advertisement';
    $aLabel[LANG_EN]['strAccent'] = 'Accent';
    $aLabel[LANG_EN]['strPremieres'] = 'Premieres';
    $aLabel[LANG_EN]['strPromoLists'] = 'Selected';
    $aLabel[LANG_EN]['strPromoNews'] = 'News';
    $aLabel[LANG_EN]['strPromoPublications'] = 'Interviews';
    $aLabel[LANG_EN]['strTodayNews'] = 'Today';
    $aLabel[LANG_EN]['strTomorrowNews'] = 'Tomorrow';
//=========================================================
// common validation labels for alertboxes
//=========================================================
    $aLabel[LANG_EN]['strEnter'] = 'Please enter ';
    $aLabel[LANG_EN]['strSelect'] = 'Please select ';
//=========================================================
// common list labels
//=========================================================
    $aLabel[LANG_EN]['strNoRecords'] = 'No items found in database.';
    $aLabel[LANG_EN]['strAll'] = 'All ';
    $aLabel[LANG_EN]['strMore'] = 'Read more';
    $aLabel[LANG_EN]['strBack'] = 'Back';
    $aLabel[LANG_EN]['strBackToList'] = 'Back to the list';
    $aLabel[LANG_EN]['strTop'] = 'Top';
    $aLabel[LANG_EN]['strNext'] = 'Next';
    $aLabel[LANG_EN]['strPrev'] = 'Previous';
    $aLabel[LANG_EN]['strRecords'] = 'showing %1 - %2 of %3 records';
    $aLabel[LANG_EN]['strGoToPage'] = 'Go to page: ';
//=========================================================
// Page labels
//=========================================================
    $aLabel[LANG_EN]['strPageID'] = 'Page ID';
    $aLabel[LANG_EN]['strParentPage'] = 'Parent page';
    $aLabel[LANG_EN]['strPageName'] = 'Page name';
    $aLabel[LANG_EN]['strMetaDescription'] = 'Meta description';
    $aLabel[LANG_EN]['strMetaKeywords'] = 'Meta keywords';
    $aLabel[LANG_EN]['strPageText'] = 'Page text';
    $aLabel[LANG_EN]['strNrViews'] = 'Nr views';
    $aLabel[LANG_EN]['strSortOrder'] = 'Page order';
    $aLabel[LANG_EN]['strLastUpdate'] = 'Last update';
    $aLabel[LANG_EN]['strTemplate'] = 'Template';
    $aLabel[LANG_EN]['strCityFilter'] = 'Show Filter';
    $aLabel[LANG_EN]['strReqUserStatus'] = 'Registered users only';
    $aLabel[LANG_EN]['strHide'] = 'Hide on website';
    $aLabel[LANG_EN]['aYesNo'] = array(0=>'No', 1=>'Yes');
    $aLabel[LANG_EN]['strPages'] = 'Pages';
    $aLabel[LANG_EN]['strRubriques'] = 'Rubriques';
//=========================================================
// Job labels
//=========================================================
    $aLabel[LANG_EN]['strJobID'] = 'Job ID';
    $aLabel[LANG_EN]['strJobTitle'] = 'Job name';
    $aLabel[LANG_EN]['strDescription'] = 'Description';
    $aLabel[LANG_EN]['strCompany'] = 'Company';
    $aLabel[LANG_EN]['strStartDate'] = 'Start date';
    $aLabel[LANG_EN]['strEndDate'] = 'End date';
    $aLabel[LANG_EN]['strRemainderDate'] = 'Date';
    $aLabel[LANG_EN]['strJobs'] = 'Jobs';
//=========================================================
// News labels
//=========================================================
    $aLabel[LANG_EN]['strNewsID'] = 'News ID';
    $aLabel[LANG_EN]['strNewsName'] = 'News name';
    $aLabel[LANG_EN]['strNewsDate'] = 'News date';
    $aLabel[LANG_EN]['strNewsLead'] = 'Intro';
    $aLabel[LANG_EN]['strNewsText'] = 'Content';
    $aLabel[LANG_EN]['strNews'] = 'News';
//=========================================================
// Publication labels
//=========================================================
    $aLabel[LANG_EN]['strPublicationID'] = 'Publication ID';
    $aLabel[LANG_EN]['strPublicationName'] = 'Publication title';
    $aLabel[LANG_EN]['strPublicationDate'] = 'Publication date';
    $aLabel[LANG_EN]['strPublicationSubtitle'] = 'Subtitle';
    $aLabel[LANG_EN]['strPublicationLead'] = 'Intro';
    $aLabel[LANG_EN]['strPublicationText'] = 'Content';
    $aLabel[LANG_EN]['strSource'] = 'Source';
    $aLabel[LANG_EN]['strSourceUrl'] = 'Source website';
    $aLabel[LANG_EN]['strAuthor'] = 'Author';
    $aLabel[LANG_EN]['strPublications'] = 'Publications';
    $aLabel[LANG_EN]['strInterviews'] = 'Interviews';
    $aLabel[LANG_EN]['strComments'] = 'Comments';
    $aLabel[LANG_EN]['strCommentID'] = 'Comment ID';
    $aLabel[LANG_EN]['strCommentTitle'] = 'Title';
    $aLabel[LANG_EN]['strCommentText'] = 'Comment';
    $aLabel[LANG_EN]['strPostComment'] = 'Post a comment';
    $aLabel[LANG_EN]['strPermalink'] = 'Link to this comment';
    $aLabel[LANG_EN]['strCommentIntro'] = 'In order to post comments, please <a href="'.setPage(USERREG_PAGE).'">register</a> and log in using your username and password.';
//=========================================================
// Festival labels
//=========================================================
    $aLabel[LANG_EN]['strFestivalID'] = 'Festival ID';
    $aLabel[LANG_EN]['strFestivalName'] = 'Festival name';
    $aLabel[LANG_EN]['strStartDate'] = 'Start date';
    $aLabel[LANG_EN]['strEndDate'] = 'End date';
    $aLabel[LANG_EN]['strFestivalLead'] = 'Intro';
    $aLabel[LANG_EN]['strFestivalText'] = 'Content';
    $aLabel[LANG_EN]['strUrl'] = 'Website';
    $aLabel[LANG_EN]['strFestivals'] = 'Festivals';
//=========================================================
// Mixer labels
//=========================================================
    $aLabel[LANG_EN]['strMixerID'] = 'ID';
    $aLabel[LANG_EN]['strParentMixer'] = 'Group';
    $aLabel[LANG_EN]['strMixerName'] = 'Title';
    $aLabel[LANG_EN]['strMixer'] = 'Mixer';
    $aLabel[LANG_EN]['aMixerTypes'] = array(1=>'Picture',
                                            2=>'City',
                                            3=>'Walk',
                                            4=>'Weekend plan',
                                            5=>'Event');

//=========================================================
// Urban labels
//=========================================================
    $aLabel[LANG_EN]['strUrbanID'] = 'ID';
    $aLabel[LANG_EN]['strUrbanName'] = 'Title';
    $aLabel[LANG_EN]['strMainUrbanName'] = 'Main Title';
    $aLabel[LANG_EN]['strUrban'] = 'Urban';
    $aLabel[LANG_EN]['strUrbanDate'] = 'Date';
    $aLabel[LANG_EN]['strUrbanText'] = 'Text';

	$aLabel[LANG_EN]['strPart1'] = 'First part';
    $aLabel[LANG_EN]['strPart2'] = 'Second part';
    $aLabel[LANG_EN]['strPart3'] = 'Third part';
    $aLabel[LANG_EN]['strPart'] = 'Part of the publication';

    $aLabel[LANG_EN]['strUrbanImage'] = 'Images';
    $aLabel[LANG_EN]['strUrbanImageFull'] = 'Image';

//=========================================================
// Multy labels
//=========================================================
    $aLabel[LANG_EN]['strMultyID'] = 'ID';
    $aLabel[LANG_EN]['strMultyName'] = 'Tile';
    $aLabel[LANG_EN]['strMulty'] = 'Publcations';
    $aLabel[LANG_EN]['strMultyDate'] = 'Date';
    $aLabel[LANG_EN]['strMultyText'] = 'Text';

	$aLabel[LANG_EN]['strCurrPart'] = 'Part';

	$aLabel[LANG_EN]['strMulties'] = 'Publications';
    $aLabel[LANG_EN]['strMultyImage'] = 'Picture';
    $aLabel[LANG_EN]['strMultyImageFull'] = 'Picture';
    //=========================================================
// Place labels
//=========================================================
    $aLabel[LANG_EN]['strPlaceID'] = 'Place ID';
    $aLabel[LANG_EN]['strPlaceName'] = 'Place name';
    $aLabel[LANG_EN]['strShortTitle'] = 'Short name';
    $aLabel[LANG_EN]['strDescription'] = 'Description';
    $aLabel[LANG_EN]['strAddress'] = 'Address';
    $aLabel[LANG_EN]['strWorkingTime'] = 'Working time';
    $aLabel[LANG_EN]['strStartTime'] = 'Starting hour';
    $aLabel[LANG_EN]['strPlaceType'] = 'Place type';
    $aLabel[LANG_EN]['strPlaceSubtype'] = 'Place subtype';
    $aLabel[LANG_EN]['strPlaces'] = 'Places';
    $aLabel[LANG_EN]['aPlaceTypes'] = array(1=>'Cinema',
                                            2=>'Theatre',
                                            3=>'Galleries, Museums, Halls',
                                            5=>'Restaurants',
                                            6=>'Clubs &amp; Bars',
                                            7=>'Other places',
                                            28=>'Activities');
                                            /*4=>'Halls',
                                            29=>'Internet clubs',
                                            32=>'Hotels');*/
    $aLabel[LANG_EN]['strCuisine'] = 'Cuisine';
    $aLabel[LANG_EN]['strAtmosphere'] = 'Atmosphere';
    $aLabel[LANG_EN]['strPriceCategory'] = 'Price Category';
    $aLabel[LANG_EN]['strMusicStyle'] = 'Music';
//=========================================================
// Place Guide labels
//=========================================================
    $aLabel[LANG_EN]['strPlaceGuideID'] = 'Place guide ID';
    $aLabel[LANG_EN]['strCategory'] = 'Category';
    $aLabel[LANG_EN]['strEntranceFee'] = 'Entrance fee';
    $aLabel[LANG_EN]['strNrSeats'] = 'Nr seats';
    $aLabel[LANG_EN]['strMusicStyle'] = 'Music style';
    $aLabel[LANG_EN]['strDJ'] = 'DJ';
    $aLabel[LANG_EN]['strLiveMusic'] = 'Live music';
    $aLabel[LANG_EN]['strKaraoke'] = 'Karaoke';
    $aLabel[LANG_EN]['strBgndMusic'] = 'Background music';
    $aLabel[LANG_EN]['strDelivery'] = 'Delivery';
    $aLabel[LANG_EN]['strFaceControl'] = 'Face control';
    $aLabel[LANG_EN]['strCuisine'] = 'Cuisine';
    $aLabel[LANG_EN]['strTerrace'] = 'Terrace / garden';
    $aLabel[LANG_EN]['strSmokingArea'] = 'Non-smoking area';
    $aLabel[LANG_EN]['strClima'] = 'Clima';
    $aLabel[LANG_EN]['strParking'] = 'Parking';
    $aLabel[LANG_EN]['strWardrobe'] = 'Wardrobe';
    $aLabel[LANG_EN]['strCardPayment'] = 'Card payment';
    $aLabel[LANG_EN]['strEntertainment'] = 'Entertainment';
    $aLabel[LANG_EN]['strWifi'] = 'Wireless internet';
    $aLabel[LANG_EN]['strNew'] = 'New';
    $aLabel[LANG_EN]['strVacation'] = 'Vacation';
    $aLabel[LANG_EN]['strVacationStartDate'] = 'Vacation start date';
    $aLabel[LANG_EN]['strVacationEndDate'] = 'Vacation end date';
//=========================================================
// Place related labels
//=========================================================
    $aLabel[LANG_EN]['strLegend'] = 'Legend';
    $aLabel[LANG_EN]['strMap'] = 'Map';
    $aLabel[LANG_EN]['strMapEditNote'] = '';
    $aLabel[LANG_EN]['strMapAddNote'] = '';
    $aLabel[LANG_EN]['strCalendar'] = 'Calendar';
    $aLabel[LANG_EN]['strNote'] = 'Note';
    $aLabel[LANG_EN]['strCalendarIntro'] = 'Please enter the preferred dates and note for the calendar reminder.';
    $aLabel[LANG_EN]['strAddToCalendar'] = 'Add To Calendar';
    $aLabel[LANG_EN]['strUpdateCalendar'] = 'Update Calendar';
    $aLabel[LANG_EN]['strDeleteCalendar'] = 'Delete From Calendar';
    $aLabel[LANG_EN]['strAddRemainderToCalendar'] = 'Add Remainder';
    $aLabel[LANG_EN]['strDoDel'] = 'Delete';
    $aLabel[LANG_EN]['strDoAdd'] = 'Add';
    $aLabel[LANG_EN]['strWeather'] = 'Weather';
    $aLabel[LANG_EN]['strProgram'] = 'Programme';
    $aLabel[LANG_EN]['strPrevMonth'] = 'Previous month programme';
    $aLabel[LANG_EN]['strPrevWeek'] = 'Previous week programme';
    $aLabel[LANG_EN]['strToday'] = 'Today programme';
    $aLabel[LANG_EN]['strThisWeek'] = 'Weekly programme';
    $aLabel[LANG_EN]['strNextWeek'] = 'Next week programme';
    $aLabel[LANG_EN]['strNextMonth'] = 'Monthly programme';
    $aLabel[LANG_EN]['strIndexDetails'] = 'Index / detailed list';
    $aLabel[LANG_EN]['strComments'] = 'Comments';
    $aLabel[LANG_EN]['strAddComment'] = 'Add comment';
    $aLabel[LANG_EN]['strVote'] = 'Vote';
    $aLabel[LANG_EN]['strRating'] = 'Rating';
    $aLabel[LANG_EN]['aRating'] = array(1=>'1',
                                        2=>'2',
                                        3=>'3',
                                        4=>'4',
                                        5=>'5',
                                        6=>'6',
                                        7=>'7',
                                        8=>'8',
                                        9=>'9',
                                        10=>'10');
    $aLabel[LANG_EN]['strTellFriend'] = 'Send to a friend';
    $aLabel[LANG_EN]['strPrice'] = 'Tickets';//'Price'
    $aLabel[LANG_EN]['strLv'] = 'BGN';
    $aLabel[LANG_EN]['strCalendarAdd'] = 'Add to Calendar';
    $aLabel[LANG_EN]['strTranslation'] = 'Translation';
    $aLabel[LANG_EN]['strOrigLanguage'] = 'Language';
    $aLabel[LANG_EN]['strGenre'] = 'Genre';
    $aLabel[LANG_EN]['strType'] = 'Type';
    $aLabel[LANG_EN]['strExhibitionGenre'] = 'Technique';
//=========================================================
// Place Hall labels
//=========================================================
    $aLabel[LANG_EN]['strPlaceHallID'] = 'Place hall ID';
    $aLabel[LANG_EN]['strHallTitle'] = 'Hall title';
    $aLabel[LANG_EN]['strHall'] = 'Hall';
//=========================================================
// Event labels
//=========================================================
    $aLabel[LANG_EN]['strEventID'] = 'Event ID';
    $aLabel[LANG_EN]['strEventName'] = 'Event title';
    $aLabel[LANG_EN]['strOriginalTitle'] = 'Original title';
    $aLabel[LANG_EN]['strEventLead'] = 'Intro';
    $aLabel[LANG_EN]['strDescription'] = 'Description';
    $aLabel[LANG_EN]['strFeatures'] = 'Year/Minutes/Country';
    $aLabel[LANG_EN]['strComment'] = 'Cast';
    $aLabel[LANG_EN]['strEventType'] = 'Event type';
    $aLabel[LANG_EN]['strEventSubtype'] = 'Event subtype';
    $aLabel[LANG_EN]['strEvents'] = 'Events';
    $aLabel[LANG_EN]['aEventTypes'] = array(10=>'Movies',
                                            11=>'Performances',
                                            12=>'Exhibitions',
                                            13=>'Classic music',
                                            14=>'Bands &amp; Musicians',
                                            21=>'Logos &amp; Other',//'Other events'
                                            24=>'Parties',
                                            27=>'Concerts');//, 30=>'CD Reviews'
    $aLabel[LANG_EN]['aAllowedEventSubtypes'] = array(
                    10=>array(1, 2, 3, 4, 5, 6, 7, 8, 9, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 42, 43, 44),
                    11=>array(11, 12, 13, 14, 15, 16, 17, 18, 45),
                    14=>array(21, 22, 23, 24, 41, 46));
//=========================================================
// Link labels
//=========================================================
    $aLabel[LANG_EN]['strLinkID'] = 'Link ID';
    $aLabel[LANG_EN]['strLinkTitle'] = 'Link name';
    $aLabel[LANG_EN]['strLinkType'] = 'Link type';
    $aLabel[LANG_EN]['aLinkTypes'] = array(1=>'Website',
                                           2=>'Order ticket at Eventim',
                                           3=>'See map at Emaps',
										   4=>'More info',
                                           0=>'Other link');
    $aLabel[LANG_EN]['strUrl'] = 'Website';
    $aLabel[LANG_EN]['strMoreInfo'] = 'MoreInfo';
	$aLabel[LANG_EN]['strLinks'] = 'Links';
//=========================================================
// E-mail labels
//=========================================================
    $aLabel[LANG_EN]['strEmailID'] = 'E-mail ID';
    $aLabel[LANG_EN]['strEmailType'] = 'E-mail type';
    $aLabel[LANG_EN]['aEmailTypes'] = array(1=>'E-mail',
                                            2=>'Private e-mail',
                                            0=>'Other e-mail');
    $aLabel[LANG_EN]['strEmail'] = 'E-mail';
    $aLabel[LANG_EN]['strEmails'] = 'E-mails';
//=========================================================
// Attachment labels
//=========================================================
    $aLabel[LANG_EN]['strAttachmentID'] = 'Attachment ID';
    $aLabel[LANG_EN]['strAttachmentTitle'] = 'Attachment title';
    $aLabel[LANG_EN]['strAttachment'] = 'Attachment';
    $aLabel[LANG_EN]['strAttachmentFull'] = 'New attachment';
    $aLabel[LANG_EN]['strAttachmentType'] = 'Attachment type';
    $aLabel[LANG_EN]['aAttachmentTypes'] = array(1=>'Old logo / title image ('.W_IMG_SMALL.'/'.H_IMG_SMALL.'px)',
                                                 2=>'Old illustration / small image ('.W_IMG_SMALL.'/'.H_IMG_SMALL.'px)',
                                                 //3=>'New title image',
                                                 4=>'Gallery ('.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)',
                                                 5=>'Panorama *.mov ('.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)',
                                                 6=>'Attached file',
                                                 7=>'Trailer *.flv');
    $aLabel[LANG_EN]['strAttachment'] = 'Attachment';
    $aLabel[LANG_EN]['strAttachments'] = 'Attachments';
//=========================================================
// Address labels
//=========================================================
    $aLabel[LANG_EN]['strAddressID'] = 'Address ID';
    $aLabel[LANG_EN]['strAddressType'] = 'Address type';
    $aLabel[LANG_EN]['aAddressTypes'] = array(1=>'Address',
                                              2=>'Mailing address',
                                              3=>'Private address');
    $aLabel[LANG_EN]['strStreet'] = 'Street';
    $aLabel[LANG_EN]['strCity'] = 'City';
    $aLabel[LANG_EN]['strZip'] = 'Zip';
    $aLabel[LANG_EN]['strAddresses'] = 'Addresses';
//=========================================================
// Phone labels
//=========================================================
    $aLabel[LANG_EN]['strPhoneID'] = 'Phone ID';
    $aLabel[LANG_EN]['strPhoneType'] = 'Phone type';
    $aLabel[LANG_EN]['aPhoneTypes'] = array(1=>'Phone',
                                            2=>'Fax',
                                            3=>'Mobile',
                                            4=>'Private',
                                            5=>'E-fax',
                                            6=>'Delivery');
    $aLabel[LANG_EN]['strArea'] = 'Area';
    $aLabel[LANG_EN]['strPhone'] = 'Phone';
    $aLabel[LANG_EN]['strExt'] = 'Ext.';
    $aLabel[LANG_EN]['strPhones'] = 'Phones';
//=========================================================
// Promotion labels
//=========================================================
    $aLabel[LANG_EN]['strPromotionID'] = 'Promotion ID';
    $aLabel[LANG_EN]['strPromotionTitle'] = 'Promotion name';
    $aLabel[LANG_EN]['strPromotionType'] = 'Promotion type';
    $aLabel[LANG_EN]['aPromotionTypes'] = array(1=>'Big accent',
                                                2=>'Small accent',
                                                3=>'Promo list',
                                                4=>'Top news');
    $aLabel[LANG_EN]['aPromotionTypesFull'] = array(
        DEF_PAGE => array(PRM_ACCENT=>'Accents', 2=>'premiere', PRM_LEFTLIST=>'featured', PRM_RIGHTLIST=>'featured (right)', PRM_INTERVIEW=>'interview'), //, PRM_NEWS=>'News'
        21 => array(PRM_ACCENT=>'accent', PRM_PREMIERE=>'premiere', PRM_LEFTLIST=>'new movies', PRM_RIGHTLIST=>'for children', PRM_INTERVIEW=>'interview'), // cinema //, PRM_NEWS=>'News'
        22 => array(PRM_ACCENT=>'accent', PRM_PREMIERE=>'featured', PRM_LEFTLIST=>'new performances', PRM_RIGHTLIST=>'for children', PRM_INTERVIEW=>'interview'), // performance //, PRM_NEWS=>'News'
        24 => array(PRM_ACCENT=>'accent', PRM_PREMIERE=>'featured', PRM_LEFTLIST=>'party time', PRM_RIGHTLIST=>'classical music', PRM_INTERVIEW=>'interview'), // music //, PRM_NEWS=>'News'
        25 => array(PRM_ACCENT=>'accent', PRM_PREMIERE=>'new exhibitions'), // exhibition //, PRM_NEWS=>'News'
        26 => array(PRM_ACCENT=>'accent', PRM_PREMIERE=>'featured places', PRM_LEFTLIST=>'new places'), // clubs & restaurants //, PRM_NEWS=>'News', PRM_RIGHTLIST=>'Food Delivery'
        28 => array(PRM_ACCENT=>'accent', PRM_LEFTLIST=>'featured', PRM_INTERVIEW=>'interview'),
	    167 => array(PRM_ACCENT=>'accent', PRM_PREMIERE=>'years ago / Х3', PRM_LEFTLIST=>'day by day', PRM_RIGHTLIST=>'the straw', PRM_EXTRA=>'traffic')
); // logos //, PRM_NEWS=>'News'
    $aLabel[LANG_EN]['aPromoEntityTypes'] = array(  $aEntityTypes[ENT_NEWS]=>$aTemplate[LANG_EN][ENT_NEWS],
                                                    $aEntityTypes[ENT_PUBLICATION]=>$aTemplate[LANG_EN][ENT_PUBLICATION],
                                                    $aEntityTypes[ENT_FESTIVAL]=>$aTemplate[LANG_EN][ENT_FESTIVAL],
                                                    $aEntityTypes[ENT_PLACE]=>$aTemplate[LANG_EN][ENT_PLACE],
                                                    $aEntityTypes[ENT_EVENT]=>$aTemplate[LANG_EN][ENT_EVENT],
													$aEntityTypes[ENT_URBAN]=>$aTemplate[LANG_EN][ENT_URBAN],
													$aEntityTypes[ENT_MULTY]=>$aTemplate[LANG_EN][ENT_MULTY]);

    $aLabel[LANG_EN]['strEntityType'] = 'Data type';
    $aLabel[LANG_EN]['strEntity'] = 'ID from selected data';
//=========================================================
// Program labels
//=========================================================
    $aLabel[LANG_EN]['strProgramID'] = 'Programme ID';
    $aLabel[LANG_EN]['strProgramType'] = 'Programme type';
    $aLabel[LANG_EN]['aProgramTypes'] = array(16=>'Film screening',
                                              15=>'Theatre performance',
                                              18=>'Gallery / Museum exhibition',
                                              19=>'Classic music',
                                              26=>'Concert',
                                              20=>'Live music',
                                              25=>'Club music & parties',
                                              23=>'Logos (books, lections, debates)');
                                              /*17=>'Video &amp; DVD',
                                              31=>'CD Premiere',
                                              23=>'More (Books, Lections, Debates)');*/
    $aLabel[LANG_EN]['strFestival'] = 'Festival';
    $aLabel[LANG_EN]['strMainPlace'] = 'Main place';
    $aLabel[LANG_EN]['strPlaceHall'] = 'Place hall';
    $aLabel[LANG_EN]['strSecondaryPlaces'] = 'Secondary places';
    $aLabel[LANG_EN]['strGuest'] = 'Guest'; //'Guest theatre'
    $aLabel[LANG_EN]['strParticipant'] = 'Artists';
    $aLabel[LANG_EN]['strAllParticipants'] = 'All Participants';
    $aLabel[LANG_EN]['strSelectedParticipants'] = 'Selected Participants';
    $aLabel[LANG_EN]['strAdd'] = 'Add >';
    $aLabel[LANG_EN]['strRemove'] = '< Remove';
    $aLabel[LANG_EN]['strMainEvent'] = 'Main event (Artist)';
    $aLabel[LANG_EN]['strSecondaryEvents'] = 'Secondary events (Artists)';
    $aLabel[LANG_EN]['strPremieres'] = 'Premieres';
    $aLabel[LANG_EN]['strPremiereType'] = 'Premiere type';
    $aLabel[LANG_EN]['aPremiereTypes'] = array(1=>'Pre-premiere',
                                              2=>'Premiere',
                                              3=>'Exclusive',
                                              4=>'Official premiere',
                                              5=>'Special screening');
    $aLabel[LANG_EN]['strProgramDatePeriodID'] = 'Period ID';
    $aLabel[LANG_EN]['strProgramDateTimeID'] = 'Date/Time ID';
    $aLabel[LANG_EN]['strProgramDate'] = 'Date';
    $aLabel[LANG_EN]['strProgramTime'] = 'Time';
    $aLabel[LANG_EN]['strNrDates'] = 'Nr dates';
    $aLabel[LANG_EN]['strNrTimes'] = 'Nr times';
    $aLabel[LANG_EN]['strGenerateGrid'] = 'Generate';
    $aLabel[LANG_EN]['strSelectPlace'] = 'Select place and hall (where happens) from list';
    $aLabel[LANG_EN]['strSelectEvent'] = 'Select event (what happens) from list';
    $aLabel[LANG_EN]['strSelected'] = 'Selected';
    $aLabel[LANG_EN]['strSelectAsPrimary'] = 'Select as primary';
    $aLabel[LANG_EN]['strSelectAsSecondary'] = 'Select as secondary';
    $aLabel[LANG_EN]['strReportType'] = 'Report type';
    $aLabel[LANG_EN]['aReportTypes'] = array(1=>'Weekly by place',
                                             2=>'Weekly by event',
                                             3=>'Daily by place',
                                             4=>'Daily by event');
    $aLabel[LANG_EN]['strProgramNote'] = 'Program note (i.e. free entrance / group price / price note for all dates)';
    $aLabel[LANG_EN]['strNote'] = 'Note';
//=========================================================
// Label labels
//=========================================================
    $aLabel[LANG_EN]['strLabelID'] = 'Category ID';
    $aLabel[LANG_EN]['strLabelName'] = 'Category title';
    $aLabel[LANG_EN]['strParentLabel'] = 'Category group';
//=========================================================
// User labels
//=========================================================
    $aLabel[LANG_EN]['strUserID'] = 'User ID';
    $aLabel[LANG_EN]['strNrLogins'] = 'Nr logins';
    $aLabel[LANG_EN]['strLastLogin'] = 'Last login';
    $aLabel[LANG_EN]['strUserStatus'] = 'User status';
//=========================================================
// Image labels
//=========================================================
    $aLabel[LANG_EN]['strPanorama'] = 'Panorama';
    $aLabel[LANG_EN]['strGallery'] = 'Photo gallery';
    $aLabel[LANG_EN]['strQuicktimePlugin'] = 'If you want to see the interactive panorama, please install <a href="http://www.apple.com/quicktime/download/" target="_blank">Quicktime Player</a>.';
    $aLabel[LANG_EN]['strPromotionImage'] = 'Promotion image';
    $aLabel[LANG_EN]['strPromotionImageFull'] = 'New promotion image<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)';
    $aLabel[LANG_EN]['strNewsImage'] = 'Main image';
    $aLabel[LANG_EN]['strNewsImageFull'] = 'New main image<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px or at least '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_EN]['strPlaceImage'] = 'Place image';
    $aLabel[LANG_EN]['strPlaceImageFull'] = 'New place image<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px or at least '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_EN]['strEventImage'] = 'Event image';
    $aLabel[LANG_EN]['strEventImageFull'] = 'New event image<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px or at least '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_EN]['strPublicationImage'] = 'Publication image';
    $aLabel[LANG_EN]['strPublicationImageFull'] = 'New publication image<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px or at least '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_EN]['strFestivalImage'] = 'Publication image';
    $aLabel[LANG_EN]['strFestivalImageFull'] = 'New publication image<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px or at least '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_EN]['strGalleryImage'] = 'Gallery images';
    $aLabel[LANG_EN]['strGalleryImageFull'] = 'New gallery image<br/>(gif, jpg, png - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px or at least '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_EN]['strGalleryImageName'] = 'Gallery image name';
    $aLabel[LANG_EN]['strGalleryImageAuthor'] = 'Gallery image artist';
//=========================================================
// File upload labels
//=========================================================
    $aLabel[LANG_EN]['strFile_'.ERR_NO_UPLOAD] = 'No image was uploaded. ';
    $aLabel[LANG_EN]['strFile_'.ERR_NONE] = 'The image was successfully uploaded. ';
    $aLabel[LANG_EN]['strFile_'.ERR_NO_FOLDER_ACCESS] = 'Write access to the upload folder is denied. ';
    $aLabel[LANG_EN]['strFile_'.ERR_NO_FILE] = 'No image was uploaded. ';
    $aLabel[LANG_EN]['strFile_'.ERR_WRONG_FILETYPE] = 'The image type is not allowed. ';
    $aLabel[LANG_EN]['strFile_'.ERR_FILE_ATTACK] = 'Unauthorised image upload. ';
//=========================================================
// common action labels used in Admin Interface
//=========================================================
    $aLabel[LANG_EN]['new_window'] = ' (in new window)';
    $aLabel[LANG_EN]['add'] = ' - add new';
    $aLabel[LANG_EN]['addmore'] = ' - add (multiple)';
    $aLabel[LANG_EN]['edit'] = 'edit';
    $aLabel[LANG_EN]['delete'] = 'delete';
    $aLabel[LANG_EN]['generate'] = 'reset password';
    $aLabel[LANG_EN]['view'] = 'view';
    $aLabel[LANG_EN]['activate'] = 'activate';
    $aLabel[LANG_EN]['deactivate'] = 'deactivate';
    $aLabel[LANG_EN]['show'] = 'show';
    $aLabel[LANG_EN]['hide'] = 'hide';
    $aLabel[LANG_EN]['list'] = ' - list all';
    $aLabel[LANG_EN]['sitemap'] = ' - sitemap tree';
    $aLabel[LANG_EN]['report'] = ' - report';
    $aLabel[LANG_EN]['up'] = 'move up';
    $aLabel[LANG_EN]['down'] = 'move down';
    $aLabel[LANG_EN]['close'] = 'Close';
    $aLabel[LANG_EN]['multiple'] = '<br /><span class="note">(hold down Ctrl for multiple selections or to unselect)</span>';
//=========================================================
// common action labels used in Admin Interface
//=========================================================
    $aLabel[LANG_EN]['strCommonData'] = 'Common data';
    $aLabel[LANG_EN]['strSelectAll'] = 'Select all';
    $aLabel[LANG_EN]['strFillAll'] = 'Fill down all';
    $aLabel[LANG_EN]['strDeleteSelected'] = 'Delete selected';
    $aLabel[LANG_EN]['strByUser'] = ' '; //' by ';
    $aLabel[LANG_EN]['strNone'] = 'None';
    $aLabel[LANG_EN]['strAny'] = 'Any';
    $aLabel[LANG_EN]['strKeyword'] = 'Keyword';
    $aLabel[LANG_EN]['strCategory'] = 'Category';
    $aLabel[LANG_EN]['strPage'] = 'Page';
    $aLabel[LANG_EN]['strWhere'] = 'Select section';
    $aLabel[LANG_EN]['strGoSearch'] = 'Find';
    $aLabel[LANG_EN]['strSearch'] = 'Search';
    $aLabel[LANG_EN]['strFilterCriteria'] = 'Search criteria';
    $aLabel[LANG_EN]['strFilterAlphabet'] = 'See in alphabet index';
    $aLabel[LANG_EN]['strFilterOrder'] = 'Order';
    $aLabel[LANG_EN]['aFilterOrders'] = array(1=>'By club', 2=>'By party');
    $aLabel[LANG_EN]['strSearchResults'] = 'Search results';
    $aLabel[LANG_EN]['strSearchRequired'] = 'Please enter search criteria.';
    $aLabel[LANG_EN]['strDeleteQ'] = "Are you sure you want to delete this?";
    $aLabel[LANG_EN]['strGenerateQ'] = "Are you sure you want to generate a new password for this user and send it to his/her e-mail address?";
    $aLabel[LANG_EN]['strSwitch'] = "Switch to: ";
    $aLabel[LANG_EN]['strSwitchQ'] = "Switching to another language will discard all changes. Do you want to continue?";
    $aLabel[LANG_EN]['strDeleteOK'] = "The data you have selected has been deleted.";
    $aLabel[LANG_EN]['strGeneratePassOK'] = "A new password was generated for this user, and account data was sent to his/her e-mail address.";
//=========================================================
// profile descriptions
//=========================================================
	$aLabel[LANG_EN]['strProfile'] = 'profile';
	$aLabel[LANG_EN]['strClassicProfile'] = 'If you prefer theater than techno and Italian cuisine rather than italo disco';
	$aLabel[LANG_EN]['strTrendyProfile'] = 'If you\'d rather catch up with dancing than documentaries and with scotch than shrimp salad';
	$aLabel[LANG_EN]['strFamilyProfile'] = 'Family deserves good entertainment too';
//=========================================================
// index label
//=========================================================
	$aLabel[LANG_EN]['strNewAddress'] = 'We have new address: Sofia 1463, Dospat street 43';
//=========================================================
// other labels
//=========================================================	
	$aLabel[LANG_EN]['strTV'] = 'TV'; 	
	$aLabel[LANG_EN]['strWeatherToday'] = 'today';
	$aLabel[LANG_EN]['strWeatherTomorrow'] = 'tomorrow';	
?>