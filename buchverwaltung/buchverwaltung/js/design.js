// Design-Modus (Hell / Dunkel / Pink)
// Die Auswahl wird im Browser (localStorage) gespeichert und gilt auf allen Seiten.

// 1) Sofort beim Laden anwenden, damit die Seite nicht kurz in der falschen Farbe aufblitzt
var gespeichert = localStorage.getItem('design') || 'hell';
document.documentElement.setAttribute('data-theme', gespeichert);

// 2) Sobald die Seite geladen ist: Auswahlfeld einstellen und auf Änderungen reagieren
document.addEventListener('DOMContentLoaded', function () 
{
  var auswahl = document.getElementById('designwahl');
  if (!auswahl) return;

  auswahl.value = gespeichert;
  auswahl.addEventListener('change', function () 
  {
    document.documentElement.setAttribute('data-theme', auswahl.value);
    localStorage.setItem('design', auswahl.value);
  });
});
