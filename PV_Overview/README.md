# Photovoltaik-Übersicht Kachel

![Bild+Variable Kachel](https://github.com/da8ter/images/blob/1c5fe63e9757e81e6d8c4c84a63e0b39fa00247c/pv_uebersicht.jpg)


### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Kachelkonfiguration](#5-Kachelkonfiguration)

### 1. Funktionsumfang

* Die "Photovoltaik-Übersicht Kachel" dient der Visualisierung von PV-Produktion, Export, Import, Eigenverbrauch inkl. Batteriespeicher für den aktuellen Tag. 
* #### Wichtig: Alle Variablen benötigen ein aktives Logging und müssen werte als kWh bereitstellen. Die Kachel bezieht die aktuellen Tageswerte aus dem Archiv! Unterschiedliche Einheiten wie kWh und wH führen zu fehlerhaften Anzeigen.
* Wie bei all meinen Kacheln kann das Aussehen weitestgehend frei konfiguriert werden. Möglich sind eigene Hintergrundbilder (inkl. einstellbarer Transparenz), Hintergrundfarben und Schriftgrößen.

### 2. Voraussetzungen

- IP-Symcon ab Version 7.1

### 3. Software-Installation

* Über den Module Store
* Über das Module Control folgende URL hinzufügen
https://github.com/da8ter/TileVisu-Photovoltaik-Uebersicht.git


### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann die Wallbox-Kachel mithilfe des Schnellfilters gefunden werden. (Suchbegriff: Photovoltaik, Übersicht, TileVisu oder Kachel)  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

### 5. Kachelkonfiguration

Grundsätzlicher Hinweis:
Standardmäßig sind alle Objekte in der Kachelansicht ausgeblendet. Sie werden nur angezeigt, wenn du sie entsprechend konfigurierst. Bitte beachte, dass nicht alle Änderungen an der Konfiguration automatisch in der Kachelansicht sichtbar sind. Sollten Änderungen nicht sofort erscheinen, lade bitte die Seite oder den iFrame neu.

### 6. Begriffsdefinition

Produktion: Der von der PV-Analge produzierte Strom.
Eigenverbrauch: Der prozentuale Anteil der PV Produktion welcher direkt im Haus selbst verbraucht wurde.
Import/Strombezug: Der Strom welcher aus dem Netz zugekauft werden musst. 
Verbrauch: Der komplett im Haus verbrauchte Strom. Egal ob aus dem Netz oder der PV-Anlage.
Eigenproduktion: Der prozentuale Anteil vom Verbrauch der durch den PV Strom abgedeckt wurde.
Export/Einspeisung: Der Strom welcher von der PV-Anlage erzeugt und in das Stromnetz eingespeist wurde.

__Kacheldesign__
Name     | Beschreibung
-------- | ------------------
Standard-Hintergrundbild|Ein-/Ausschalten des Standard-Hintergrundbildes.
Hintergrundbild|Auswahl eines eigenen Medienobjekts als Hintergrund.
Transparenz Bild|Einstellung der Transparenz des Hintergrundbildes, um es abzudunkeln oder farblich anzupassen. 
Kachelhintergrundfarbe|Farbe des Kachelhintergrunds (wird nur bei eingestellter Bildtransparenz sichtbar)
Eckenradius|Der Eckenradius der Balken in Pixel

__Balkendesign__
Name     | Beschreibung
-------- | ------------------
Schriftfarbe Balken|Schriftfarbe aller Texte in den Balken.
Schriftfarbe Zusatzinfos|Schriftfarbe aller Texte außerhalb Balken.
Farbe Export/Einspeisung|Balkenfarbe für den Export/Eispeisung. 
Farbe Import|Balkenfarbe für den Import/Strombezug. 
Farbe 1 Balken Eigenverbrauch|Farbe eins vom Balken-Farbverlauf für die Eingenverbrauchsanzeige.
Farbe 2 Balken Eigenverbrauch|Farbe zwei vom Balken-Farbverlauf für die Eingenverbrauchsanzeige.
Farbe 1 Balken Eigenproduktion|Farbe eins vom Balken-Farbverlauf für die Eigenproduktion.
Farbe 2 Balken Eigenproduktion|Farbe zwei vom Balken-Farbverlauf für die Eigenproduktion.

__Variable__
Name     | Beschreibung
-------- | ------------------
Verbrauch berechnen|Berechnet den Tagesverbrauch aus PV-Produktion und Strombezug (import).
Produktion/Erzeugung|Zählervariable für die PV-Produktion in kWh.
Label Produktion/Erzeugung|Text der in der Kachel angezeigt werden soll.
Enladung Speicher|Zählervariable für die aus einem Batteriespeicher entladenen kWh.
Beladung Speicher|Zählervariable für die in einen Batteriespeicher geladenen kWh.
Label Verbruch|Text der in der Kachel angezeigt werden soll.
Import/Bezug|Zählervariable für den Strombezug (import) in kWh.
Label Import/Bezug|Text der in der Kachel angezeigt werden soll.
Export/Einspeisung|Zählervariable für die Einspeisung (export) in kWh.
Label Export/Einspeisung|Text der in der Kachel angezeigt werden soll.
Label Eigenproduktion|Text der in der Kachel angezeigt werden soll.
Label Eigenverbrauch|Text der in der Kachel angezeigt werden soll
