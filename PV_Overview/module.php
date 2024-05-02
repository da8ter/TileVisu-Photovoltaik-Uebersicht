<?php
class TileVisuPhotovoltaikOverviewTile extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();

        $this->RegisterPropertyInteger("ProduktionWert", 0);
        $this->RegisterPropertyString("ProduktionLabel", "Produktion");
        $this->RegisterPropertyInteger("SpeicherEntladungWert", 0);
        $this->RegisterPropertyInteger("SpeicherBeladungWert", 0);
        $this->RegisterPropertyInteger("ExportWert", 0);
        $this->RegisterPropertyString("ExportLabel", "Export");
        $this->RegisterPropertyInteger("VerbrauchWert", 0);
        $this->RegisterPropertyString("VerbrauchLabel", "Verbrauch");
        $this->RegisterPropertyBoolean("VerbrauchBerechnen", 0);
        $this->RegisterPropertyInteger("ImportWert", 0);
        $this->RegisterPropertyString("ImportLabel", "Import");
        $this->RegisterPropertyString("EigenverbrauchLabel", "Eigenverbrauch");
        $this->RegisterPropertyString("EigenproduktionLabel", "Eigenproduktion");
        $this->RegisterPropertyInteger("EigenverbrauchVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("EigenverbrauchVerlaufFarbe2", 2132596);
        $this->RegisterPropertyInteger("EigenproduktionVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("EigenproduktionVerlaufFarbe2", 2132596);
        $this->RegisterPropertyInteger("ButtonFarbe1", 2674091);
        $this->RegisterPropertyInteger("ButtonFarbe2", 6579300);
        //Kachellayout
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyFloat("Bildtransparenz", 0.7);
        $this->RegisterPropertyInteger("Kachelhintergrundfarbe", -1);
        $this->RegisterPropertyBoolean("BG_Off", 1);
        $this->RegisterPropertyInteger("SchriftfarbeBalken", 0xFFFFFF);
        $this->RegisterPropertyInteger("SchriftfarbeSub", 0xFFFFFF);
        $this->RegisterPropertyFloat("SchriftgroesseBalken", 1);
        $this->RegisterPropertyFloat("SchriftgroesseSub", 0.8);
        $this->RegisterPropertyFloat("Eckenradius", 6);
        $this->RegisterPropertyInteger("EinspeisungFarbe", 2598689);
        $this->RegisterPropertyInteger("ZukaufFarbe", 9830400);
        $this->RegisterAttributeInteger("Zeitraum", 1);
        $this->RegisterVariableInteger("ZeitraumStart", "Zeitraum Start" , "UnixTimestamp", 1);
        $this->EnableAction("ZeitraumStart");
        $this->RegisterVariableInteger("ZeitraumEnde", "Zeitraum Ende" , "UnixTimestamp", 2);
        $this->EnableAction("ZeitraumEnde");
        $this->RegisterPropertyBoolean("ButtonSwitch", 0);
        // Visualisierungstyp auf 1 setzen, da wir HTML anbieten möchten
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();


                //Referenzen Registrieren
                $ids = [
                    $this->ReadPropertyInteger('ProduktionWert'),
                    $this->ReadPropertyInteger('SpeicherEntladungWert'),
                    $this->ReadPropertyInteger('SpeicherBeladungWert'),
                    $this->ReadPropertyInteger('ExportWert'),
                    $this->ReadPropertyInteger('VerbrauchWert'),
                    $this->ReadPropertyInteger('ImportWert'),
                    $this->ReadPropertyInteger('bgImage')
                ];
                $refs = $this->GetReferenceList();
                    foreach($refs as $ref) {
                        $this->UnregisterReference($ref);
                    } 
                    foreach ($ids as $id) {
                        if ($id !== '') {
                            $this->RegisterReference($id);
                        }
                    }

        // Aktualisiere registrierte Nachrichten
        foreach ($this->GetMessageList() as $senderID => $messageIDs)
        {
            foreach ($messageIDs as $messageID)
            {
                $this->UnregisterMessage($senderID, $messageID);  //
            }
        }


        foreach (['ProduktionWert', 'SpeicherEntladungWert', 'SpeicherBeladungWert', 'ExportWert', 'VerbrauchWert', 'ImportWert'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }
        $this->RegisterMessage($this->ReadAttributeInteger('Zeitraum'), VM_UPDATE);

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['ProduktionWert', 'SpeicherEntladungWert', 'SpeicherBeladungWert', 'ExportWert', 'VerbrauchWert', 'ImportWert'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                

                switch ($Message)
                {
                    case VM_UPDATE:
                        
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                        //$this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
                        //$this->UpdateVisualizationValue(json_encode([$VariableProperty . 'Value' => GetValue($this->ReadPropertyInteger($VariableProperty))]));

                        $archivID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];


                        $SpeicherEntladungID = $this->ReadPropertyInteger('SpeicherEntladungWert');
                        $entladungSpeicher = 0; // Standardwert setzen 
                        $userStart = $this->GetValue("ZeitraumStart");
                        $userEnde = $this->GetValue("ZeitraumEnde");


                        if (empty($userStart)) {
                            $userStart = strtotime("today 00:00");
                        }
                        
                        if (empty($userEnde)) {
                            $userEnde = time();
                        }

                        
                        $startzeit = $this->ReadAttributeInteger('Zeitraum');

                        if ($startzeit == 1) {
                            $zeit = strtotime("today 00:00");
                            $ende = time();
                            $aggregation = 1;
                            }
                            elseif ($startzeit == 2) {
                            $zeit = strtotime("Monday this week 00:00");
                            $aggregation = 1;
                            $ende = time();
                            }
                            elseif ($startzeit == 3) {
                            $zeit = strtotime("first day of this month 00:00");
                            $aggregation = 1;
                            $ende = time();
                            }
                            elseif ($startzeit == 4) {
                            $zeit = strtotime("first day of January this year 00:00");
                            $aggregation = 1;
                            $ende = time();
                            }
                            elseif ($startzeit == 5) {
                            $zeit = $userStart;
                            $aggregation = 1;
                            $ende = $userEnde;
                            }
                            else  {
                            $zeit = strtotime("today 00:00");
                            $aggregation = 1;
                            $ende = time();
                            }


                         if (IPS_VariableExists($SpeicherEntladungID) && AC_GetLoggingStatus($archivID, $SpeicherEntladungID)) {
                            $SpeicherEntladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherEntladungID, $aggregation, $zeit, $ende, 0);
                            if (!empty($SpeicherEntladung_heute_archiv)) {
                                foreach($SpeicherEntladung_heute_archiv as $Gesamt) {
                                    $SpeicherEntladung_Avg[] = $Gesamt['Avg'];
                                }
                                //Array auf Inhalt prüfen 
                                if (!empty($SpeicherEntladung_Avg)) 
                                {
                                $SpeicherEntladung_kWh = array_sum($SpeicherEntladung_Avg);
                                }
                                else {
                                    $SpeicherEntladung_kWh = 0;
                                }
                                $entladungSpeicher = round($SpeicherEntladung_kWh, 2);
                            }
                        }
                        $SpeicherBeladungID = $this->ReadPropertyInteger('SpeicherBeladungWert');
                        $beladungSpeicher = 0; // Standardwert setzen 
            
                        if (IPS_VariableExists($SpeicherBeladungID) && AC_GetLoggingStatus($archivID, $SpeicherBeladungID)) {
                            $SpeicherBeladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherBeladungID, $aggregation, $zeit, $ende, 0);
                            if (!empty($SpeicherBeladung_heute_archiv)) {
                                foreach($SpeicherBeladung_heute_archiv as $Gesamt) {
                                    $SpeicherBeladung_Avg[] = $Gesamt['Avg'];
                                }
                                //Array auf Inhalt prüfen 
                                if (!empty($SpeicherBeladung_Avg)) 
                                {
                                $SpeicherBeladung_kWh = array_sum($SpeicherBeladung_Avg);
                                }
                                else {
                                    $SpeicherBeladung_kWh = 0;
                                }
                                $beladungSpeicher = round($SpeicherBeladung_kWh, 2);
                            }
                        }
            
                        $produktionsID = $this->ReadPropertyInteger('ProduktionWert');
                        $produktion = 0; // Standardwert setzen
                        
                        if (IPS_VariableExists($produktionsID) && AC_GetLoggingStatus($archivID, $produktionsID)) {
                            $produktion_heute_archiv = AC_GetAggregatedValues($archivID, $produktionsID, $aggregation, $zeit, $ende, 0);
                            if (!empty($produktion_heute_archiv)) {
                                foreach($produktion_heute_archiv as $Gesamt) {
                                    $produktion_heute_Avg[] = $Gesamt['Avg'];
                                }
                                //Array auf Inhalt prüfen 
                                if (!empty($produktion_heute_Avg)) 
                                {
                                $produktion_heute_kWh = array_sum($produktion_heute_Avg);
                                }
                                else {
                                    $produktion_heute_kWh = 0;
                                }
                                $produktion = round($produktion_heute_kWh, 2);
                            }
                        }
            
            
                        $importID = $this->ReadPropertyInteger('ImportWert');
                        $import = 0; // Standardwert setzen
                        
                        if (IPS_VariableExists($importID) && AC_GetLoggingStatus($archivID, $importID)) {
                            $import_heute_archiv = AC_GetAggregatedValues($archivID, $importID, $aggregation, $zeit, $ende, 0);
                            if (!empty($import_heute_archiv)) {
                                foreach($import_heute_archiv as $Gesamt) {
                                    $import_heute_Avg[] = $Gesamt['Avg'];
                                }
                                //Array auf Inhalt prüfen 
                                if (!empty($import_heute_Avg)) 
                                {
                                $import_heute_kWh = array_sum($import_heute_Avg);
                                }
                                else {
                                    $import_heute_kWh = 0;
                                }
                                $import = round($import_heute_kWh, 2);
                            }
                        }
            
                        $verbrauchID = $this->ReadPropertyInteger('VerbrauchWert');
                        $verbrauch = 0; // Standardwert setzen
                        
                        if (IPS_VariableExists($verbrauchID) && AC_GetLoggingStatus($archivID, $verbrauchID)) {
                            $verbrauch_heute_archiv = AC_GetAggregatedValues($archivID, $verbrauchID, $aggregation, $zeit, $ende, 0);
                            if (!empty($verbrauch_heute_archiv)) {
                                foreach($verbrauch_heute_archiv as $Gesamt) {
                                    $verbrauch_heute_Avg[] = $Gesamt['Avg'];
                                }
                                //Array auf Inhalt prüfen 
                                if (!empty($verbrauch_heute_Avg)) 
                                {
                                $verbrauch_heute_kWh = array_sum($verbrauch_heute_Avg);
                                }
                                else {
                                    $verbrauch_heute_kWh = 0;
                                }
                                $verbrauch= round($verbrauch_heute_kWh, 2);
                            }
                                                        
                        }
            
                        $exportID = $this->ReadPropertyInteger('ExportWert');
                        $export = 0; // Standardwert setzen
                        
                        if (IPS_VariableExists($exportID) && AC_GetLoggingStatus($archivID, $exportID)) {
                            $export_heute_archiv = AC_GetAggregatedValues($archivID, $exportID, $aggregation, $zeit, $ende, 0);
                            if (!empty($export_heute_archiv)) {
                                foreach($export_heute_archiv as $Gesamt) {
                                    $export_heute_Avg[] = $Gesamt['Avg'];
                                }
                                //Array auf Inhalt prüfen 
                                if (!empty($export_heute_Avg)) 
                                {
                                $export_heute_kWh = array_sum($export_heute_Avg);
                                }
                                else {
                                    $export_heute_kWh = 0;
                                }
                                $export= round($export_heute_kWh, 2);
                            }
                        }
            
            
            
            
                        // Eingabewerte
                        //$produktion = 63; // in kWh
                        //$beladungSpeicher = 27; // in kWh
                        //$entladungSpeicher = 0.1; // in kWh
                        //$import = 6.8; // in kWh
                        //$export = 7.3; // in kWh
                        //$verbrauch = 35.5;

                        // Berechnungen
                        $eigenverbrauch = round(($produktion - $export), 2);
                        $eigenproduktion = round(($produktion - $export - $beladungSpeicher) + $entladungSpeicher, 2);

                        if ($this->ReadPropertyBoolean('VerbrauchBerechnen') == true) {
                            $verbrauch = round($produktion - $export - $beladungSpeicher + $entladungSpeicher + $import, 2);
                                                      
                        }

                        // Vermeidung von Division durch Null und Berechnung der Prozentwerte
                        $eigenproduktion_prozent = $verbrauch > 0 ? round(($eigenproduktion / $verbrauch) * 100, 2) : 0;
                        $eigenproduktion_speicher_prozent = $verbrauch > 0 ? round(($entladungSpeicher / $verbrauch) * 100, 2) : 0;
                        $import_prozent = $verbrauch > 0 ? round(($import / $verbrauch) * 100, 2) : 0;
                        $export_prozent = $produktion > 0 ? round(($export / $produktion) * 100, 2) : 0;
                        $eigenverbrauch_prozent = $produktion > 0 ? round(($eigenverbrauch / $produktion) * 100, 2) : 0;  






                        $this->UpdateVisualizationValue(json_encode(['produktion' => $produktion]));
                        $this->UpdateVisualizationValue(json_encode(['speicherentladungwert' => $entladungSpeicher]));
                        $this->UpdateVisualizationValue(json_encode(['speicherbeladungwert' => $beladungSpeicher]));
                        $this->UpdateVisualizationValue(json_encode(['import' => $import]));
                        $this->UpdateVisualizationValue(json_encode(['verbrauch' => $verbrauch]));
                        $this->UpdateVisualizationValue(json_encode(['export' => $export]));
                        $this->UpdateVisualizationValue(json_encode(['export_prozent' => $export_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['import_prozent' => $import_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenverbrauch_prozent' => $eigenverbrauch_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenproduktion_prozent' => $eigenproduktion_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenproduktion_prozent_ohne_speicher' => $eigenproduktion_prozent - $eigenproduktion_speicher_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenproduktion_speicher_prozent' => $eigenproduktion_speicher_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenverbrauch' => $eigenverbrauch]));
                        $this->UpdateVisualizationValue(json_encode(['eigenproduktion' => $eigenproduktion]));
                        $this->UpdateVisualizationValue(json_encode(['zeitraum' => $this->ReadAttributeInteger('Zeitraum')]));
                       
                        
                        break; // Beende die Schleife, da der passende Wert gefunden wurdea

                }
            }
            $this->UpdateVisualizationValue(json_encode(['zeitraum' => $this->ReadAttributeInteger('Zeitraum')]));
            $this->UpdateVisualizationValue(json_encode(['buttonswitch' => $this->ReadPropertyBoolean('ButtonSwitch')]));
        }
    }


    public function RequestAction($Ident, $Value) {

        switch($Ident) {
            case "Zeitraum":
                $this->WriteAttributeInteger($Ident, $Value);
                break;
            case "ZeitraumStart":
                SetValue($this->GetIDForIdent($Ident), $Value);
                break;
            case "ZeitraumEnde":
                SetValue($this->GetIDForIdent($Ident), $Value);
                break;
            default:
        }





        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }
    


    public function GetVisualizationTile()
    {
        // Füge ein Skript hinzu, um beim Laden, analog zu Änderungen bei Laufzeit, die Werte zu setzen
        $initialHandling = '<script>handleMessage(' . json_encode($this->GetFullUpdateMessage()) . ')</script>';

        // Füge statisches HTML aus Datei hinzu
        $module = file_get_contents(__DIR__ . '/module.html');

        // Gebe alles zurück.
        // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage erst im HTML definiert wird
        return $module . $initialHandling;
    }



    private function GetFullUpdateMessage() {

        $result = [];
            $result['eigenverbrauchverlauffarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenverbrauchVerlaufFarbe1'));
            $result['eigenverbrauchverlauffarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenverbrauchVerlaufFarbe2'));
            $result['eigenproduktionverlauffarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenproduktionVerlaufFarbe1'));
            $result['eigenproduktionverlauffarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenproduktionVerlaufFarbe2'));
            $result['buttonfarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('ButtonFarbe1'));
            $result['buttonfarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('ButtonFarbe2'));
            $result['bildtransparenz'] = $this->ReadPropertyFloat('Bildtransparenz');
            $result['kachelhintergrundfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('Kachelhintergrundfarbe'));
            $result['schriftfarbebalken'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('SchriftfarbeBalken'));
            $result['schriftfarbesub'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('SchriftfarbeSub'));
            $result['schriftgroessebalken'] = $this->ReadPropertyFloat('SchriftgroesseBalken');
            $result['schriftgroessesub'] = $this->ReadPropertyFloat('SchriftgroesseSub');
            $result['eckenradius'] = $this->ReadPropertyFloat('Eckenradius');
            $result['einspeisungfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EinspeisungFarbe'));
            $result['zukauffarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('ZukaufFarbe'));
            $result['produktionlabel'] = $this->ReadPropertyString('ProduktionLabel');
            $result['exportlabel'] = $this->ReadPropertyString('ExportLabel');
            $result['importlabel'] = $this->ReadPropertyString('ImportLabel');
            $result['verbrauchlabel'] = $this->ReadPropertyString('VerbrauchLabel');
            $result['eigenverbrauchlabel'] = $this->ReadPropertyString('EigenverbrauchLabel');
            $result['eigenproduktionlabel'] = $this->ReadPropertyString('EigenproduktionLabel');
            $result['zeitraum'] =  $this->ReadAttributeInteger('Zeitraum');
            $result['buttonswitch'] = $this->ReadPropertyBoolean('ButtonSwitch');

            
            $archivID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];



            $SpeicherEntladungID = $this->ReadPropertyInteger('SpeicherEntladungWert');
            $entladungSpeicher = 0; // Standardwert setzen 

            $userStart = $this->GetValue("ZeitraumStart");
            $userEnde = $this->GetValue("ZeitraumEnde");

            if (empty($userStart)) {
                $userStart = strtotime("today 00:00");
            }
            
            if (empty($userEnde)) {
                $userEnde = time();
            }

            
            $startzeit = $this->ReadAttributeInteger('Zeitraum');

            if ($startzeit == 1) {
                $zeit = strtotime("today 00:00");
                $ende = time();
                $aggregation = 1;
                }
                elseif ($startzeit == 2) {
                $zeit = strtotime("Monday this week 00:01");
                $aggregation = 1;
                $ende = time();
                }
                elseif ($startzeit == 3) {
                $zeit = strtotime("first day of this month 00:01");
                $aggregation = 1;
                $ende = time();
                }
                elseif ($startzeit == 4) {
                $zeit = strtotime("first day of January this year 00:00");
                $aggregation = 1;
                $ende = time();
                }
                elseif ($startzeit == 5) {
                $zeit = $userStart;
                $aggregation = 1;
                $ende = $userEnde;
                }
                else  {
                $zeit = strtotime("today 00:00");
                $aggregation = 1;
                $ende = time();
                }
var_dump($zeit);

            if (IPS_VariableExists($SpeicherEntladungID) && AC_GetLoggingStatus($archivID, $SpeicherEntladungID)) {
                $SpeicherEntladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherEntladungID, $aggregation, $zeit, $ende, 0);
                if (!empty($SpeicherEntladung_heute_archiv)) {
                    foreach($SpeicherEntladung_heute_archiv as $Gesamt) {
                        $SpeicherEntladung_Avg[] = $Gesamt['Avg'];
                    }
                    //Array auf Inhalt prüfen 
                    if (!empty($SpeicherEntladung_Avg)) 
                    {
                    $SpeicherEntladung_kWh = array_sum($SpeicherEntladung_Avg);
                    }
                    else {
                        $SpeicherEntladung_kWh = 0;
                    }
                    $entladungSpeicher = round($SpeicherEntladung_kWh, 2);
                }
            }
            $SpeicherBeladungID = $this->ReadPropertyInteger('SpeicherBeladungWert');
            $beladungSpeicher = 0; // Standardwert setzen 

            if (IPS_VariableExists($SpeicherBeladungID) && AC_GetLoggingStatus($archivID, $SpeicherBeladungID)) {
                $SpeicherBeladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherBeladungID, $aggregation, $zeit, $ende, 0);
                if (!empty($SpeicherBeladung_heute_archiv)) {
                    foreach($SpeicherBeladung_heute_archiv as $Gesamt) {
                        $SpeicherBeladung_Avg[] = $Gesamt['Avg'];
                    }
                    //Array auf Inhalt prüfen 
                    if (!empty($SpeicherBeladung_Avg)) 
                    {
                    $SpeicherBeladung_kWh = array_sum($SpeicherBeladung_Avg);
                    }
                    else {
                        $SpeicherBeladung_kWh = 0;
                    }
                    $beladungSpeicher = round($SpeicherBeladung_kWh, 2);
                }
            }

            $produktionsID = $this->ReadPropertyInteger('ProduktionWert');
            $produktion = 0; // Standardwert setzen
            
            if (IPS_VariableExists($produktionsID) && AC_GetLoggingStatus($archivID, $produktionsID)) {
                $produktion_heute_archiv = AC_GetAggregatedValues($archivID, $produktionsID, $aggregation, $zeit, $ende, 0);
                if (!empty($produktion_heute_archiv)) {
                    foreach($produktion_heute_archiv as $Gesamt) {
                        $produktion_heute_Avg[] = $Gesamt['Avg'];
                    }
                    //Array auf Inhalt prüfen 
                    if (!empty($produktion_heute_Avg)) 
                    {
                    $produktion_heute_kWh = array_sum($produktion_heute_Avg);
                    }
                    else {
                        $produktion_heute_kWh = 0;
                    }
                    $produktion = round($produktion_heute_kWh, 2);
                }
            }


            $importID = $this->ReadPropertyInteger('ImportWert');
            $import = 0; // Standardwert setzen
            
            if (IPS_VariableExists($importID) && AC_GetLoggingStatus($archivID, $importID)) {
                $import_heute_archiv = AC_GetAggregatedValues($archivID, $importID, $aggregation, $zeit, $ende, 0);
                if (!empty($import_heute_archiv)) {
                    foreach($import_heute_archiv as $Gesamt) {
                        $import_heute_Avg[] = $Gesamt['Avg'];
                    }
                    //Array auf Inhalt prüfen 
                    if (!empty($import_heute_Avg)) 
                    {
                    $import_heute_kWh = array_sum($import_heute_Avg);
                    }
                    else {
                        $import_heute_kWh = 0;
                    }
                    $import = round($import_heute_kWh, 2);
                }
            }

            $verbrauchID = $this->ReadPropertyInteger('VerbrauchWert');
            $verbrauch = 0; // Standardwert setzen
            
            if (IPS_VariableExists($verbrauchID) && AC_GetLoggingStatus($archivID, $verbrauchID)) {
                $verbrauch_heute_archiv = AC_GetAggregatedValues($archivID, $verbrauchID, $aggregation, $zeit, $ende, 0);
                if (!empty($verbrauch_heute_archiv)) {
                    foreach($verbrauch_heute_archiv as $Gesamt) {
                        $verbrauch_heute_Avg[] = $Gesamt['Avg'];
                    }
                    //Array auf Inhalt prüfen 
                    if (!empty($verbrauch_heute_Avg)) 
                    {
                    $verbrauch_heute_kWh = array_sum($verbrauch_heute_Avg);
                    }
                    else {
                        $verbrauch_heute_kWh = 0;
                    }
                    $verbrauch= round($verbrauch_heute_kWh, 2);
                }
                                            
            }

            $exportID = $this->ReadPropertyInteger('ExportWert');
            $export = 0; // Standardwert setzen
            
            if (IPS_VariableExists($exportID) && AC_GetLoggingStatus($archivID, $exportID)) {
                $export_heute_archiv = AC_GetAggregatedValues($archivID, $exportID, $aggregation, $zeit, $ende, 0);
                if (!empty($export_heute_archiv)) {
                    foreach($export_heute_archiv as $Gesamt) {
                        $export_heute_Avg[] = $Gesamt['Avg'];
                    }
                    //Array auf Inhalt prüfen 
                    if (!empty($export_heute_Avg)) 
                    {
                    $export_heute_kWh = array_sum($export_heute_Avg);
                    }
                    else {
                        $export_heute_kWh = 0;
                    }
                    $export= round($export_heute_kWh, 2);
                }
            }




            // Eingabewerte
            //$produktion = 63; // in kWh
            //$beladungSpeicher = 27; // in kWh
            //$entladungSpeicher = 5; // in kWh
            //$import = 6.8; // in kWh
            //$export = 7.3; // in kWh
            //$verbrauch = 35.5;
            //$test = 10;

            // Berechnungen
            $eigenverbrauch = round(($produktion - $export), 2);
            $eigenproduktion = round(($produktion - $export - $beladungSpeicher) + $entladungSpeicher, 2);


            if ($this->ReadPropertyBoolean('VerbrauchBerechnen') == true) {
                $verbrauch = round($produktion - $export - $beladungSpeicher + $entladungSpeicher + $import, 2);
                                          
            }


            // Vermeidung von Division durch Null und Berechnung der Prozentwerte
            $eigenproduktion_prozent = $verbrauch > 0 ? round(($eigenproduktion / $verbrauch) * 100, 2) : 0;
            $eigenproduktion_speicher_prozent = $verbrauch > 0 ? round(($entladungSpeicher / $verbrauch) * 100, 2) : 0;
            $import_prozent = $verbrauch > 0 ? round(($import / $verbrauch) * 100, 2) : 0;
            $export_prozent = $produktion > 0 ? round(($export / $produktion) * 100, 2) : 0;
            $eigenverbrauch_prozent = $produktion > 0 ? round(($eigenverbrauch / $produktion) * 100, 2) : 0;  


            
            $result['produktion'] = $produktion;
            $result['speicherentladungwert'] = $entladungSpeicher;
            $result['speicherbeladungwert'] = $beladungSpeicher;
            $result['export'] = $export;
            $result['import'] = $import; 
            $result['verbrauch'] = $verbrauch; 

            $result['export_prozent'] = $export_prozent;
            $result['import_prozent'] = $import_prozent;
            $result['eigenverbrauch_prozent'] = $eigenverbrauch_prozent;
            $result['eigenproduktion_speicher_prozent'] = $eigenproduktion_speicher_prozent;
            $result['eigenproduktion_prozent'] = $eigenproduktion_prozent;
            $result['eigenproduktion_prozent_ohne_speicher'] = $eigenproduktion_prozent - $eigenproduktion_speicher_prozent;
            $result['eigenverbrauch'] =  $eigenverbrauch;
            $result['eigenproduktion'] =  $eigenproduktion;



            $imageID = $this->ReadPropertyInteger('bgImage');
            if (IPS_MediaExists($imageID)) {
                $image = IPS_GetMedia($imageID);
                if ($image['MediaType'] === MEDIATYPE_IMAGE) {
                    $imageFile = explode('.', $image['MediaFile']);
                    $imageContent = '';
                    // Falls ja, ermittle den Anfang der src basierend auf dem Dateitypen
                    switch (end($imageFile)) {
                        case 'bmp':
                            $imageContent = 'data:image/bmp;base64,';
                            break;
    
                        case 'jpg':
                        case 'jpeg':
                            $imageContent = 'data:image/jpeg;base64,';
                            break;
    
                        case 'gif':
                            $imageContent = 'data:image/gif;base64,';
                            break;
    
                        case 'png':
                            $imageContent = 'data:image/png;base64,';
                            break;
    
                        case 'ico':
                            $imageContent = 'data:image/x-icon;base64,';
                            break;
                    }

                    // Nur fortfahren, falls Inhalt gesetzt wurde. Ansonsten ist das Bild kein unterstützter Dateityp
                    if ($imageContent) {
                        // Hänge base64-codierten Inhalt des Bildes an
                        $imageContent .= IPS_GetMediaContent($imageID);
                        $result['image1'] = $imageContent;
                    }

                }
            }
            else{
                $imageContent = 'data:image/png;base64,';
                $imageContent .= base64_encode(file_get_contents(__DIR__ . '/../imgs/kachelhintergrund1.png'));


                if ($this->ReadPropertyBoolean('BG_Off')) {
                    $result['image1'] = $imageContent;
                }
            }     

            $imagespeicher = 'data:image/png;base64,';
            $imagespeicher .= base64_encode(file_get_contents(__DIR__ . '/assets/speicher.png'));
            $result['image_speicher'] = $imagespeicher;

            $imagepv = 'data:image/png;base64,';
            $imagepv .= base64_encode(file_get_contents(__DIR__ . '/assets/pv.png'));
            $result['image_pv'] = $imagepv;




        return json_encode($result);
    }



    private function CheckAndGetValueFormatted($property) {
        $id = $this->ReadPropertyInteger($property);
        if (IPS_VariableExists($id)) {
            return GetValueFormatted($id);
        }
        return false;
    }


    private function GetColor($id) {
        $variable = IPS_GetVariable($id);
        $Value = GetValue($id);
        $profile = $variable['VariableCustomProfile'] ?: $variable['VariableProfile'];

        if ($profile && IPS_VariableProfileExists($profile)) {
            $p = IPS_GetVariableProfile($profile);
            
            foreach ($p['Associations'] as $association) {
                if (isset($association['Value'], $association['Color']) && $association['Value'] == $Value) {
                    return $association['Color'] === -1 ? "" : sprintf('%06X', $association['Color']);
                    
                }
            }
        }
        return "";
    }


    private function GetColorRGB($hexcolor) {
        $transparenz = $this->ReadPropertyFloat('InfoMenueTransparenz');
        if($hexcolor != "-1")
        {
                $hexColor = sprintf('%06X', $hexcolor);
                // Prüft, ob der Hex-Farbwert gültig ist
                if (strlen($hexColor) == 6) {
                    $r = hexdec(substr($hexColor, 0, 2));
                    $g = hexdec(substr($hexColor, 2, 2));
                    $b = hexdec(substr($hexColor, 4, 2));
                    return "rgba($r, $g, $b, $transparenz)";
                } else {
                    // Fallback für ungültige Eingaben
                    return $hexColor;
                }
        }
        else {
            return "";
        }
    }

    private function GetIcon($id, $varicon) {
        $variable = IPS_GetVariable($id);
        $Value = GetValue($id);
        $icon = "";
        //Abfragen ob das Variablen-Icon oder das Profil-Icon verwendet werden soll
        if($varicon == true){
        $icon = IPS_GetObject($id);
            if($icon['ObjectIcon'] != ""){
                $icon = $icon['ObjectIcon'];
            }
            else {
                $icon = "Transparent";
            }
        }
        else {
        // Profil-Icon abrufen
        $profile = $variable['VariableCustomProfile'] ?: $variable['VariableProfile'];
        $icon = "";

        if ($profile && IPS_VariableProfileExists($profile)) {
            $p = IPS_GetVariableProfile($profile);

            foreach ($p['Associations'] as $association) {
                if (isset($association['Value']) && $association['Icon'] != "" && $association['Value'] == $Value) {
                    $icon = $association['Icon'];
                    break;
                }
            }

            if ($icon == "" && isset($p['Icon']) && $p['Icon'] != "") {
                $icon = $p['Icon'];
            }

            if ($icon == "") {
                $icon = "Transparent";
            }
        }
        else {
            $icon = "Transparent";
        }
        
        }
        return $icon;
    }


    public function UpdateVisible($Visible) {
        $this->UpdateFormField('VerbrauchWert', 'visible', !$Visible);
    }

}
?>