# Allegro_REST_RSS
Skrypt do tworzenia kanałów RSS. Wykorzystuje REST API Allegro (WebAPI ma zostać wygaszone - dotychczasowe rozwiązania z którymi się spotkałem opierają się na starym rozwiązaniu).

Stworzone na podstawie [php-allegro-rest-api](https://github.com/Wiatrogon/php-allegro-rest-api) użytkownika @Wiatrogon oraz wykorzystuje rozwiązania (i sporą część instrukcji :)) projektu [alleRSS](https://github.com/iskuzik/alleRSS) od @iskuzik

Skrypt używa publicznej metody **GET offers/listing**, tak więc nadaje się to do wykorzystania w generowaniu kanału RSS - część zasobów np. wymaga autoryzacji użytkownika. Jest to duża zmiana w stosunku do WebAPI.
W tym przypadku do działania jest potrzebny **clientID** oraz **clientSecret** dla **DeviceFlow** (o tym poniżej)

Mimo wszystko rozwiązanie nie jest tak dobre jak oryginalny generator RSS od allegro (wygaszony w marcu 2018) - więcej o tym w [uwagach](#Uwagi)

# Instalacja
1. Pobieramy skrypt 
1. Dodajemy do skryptu nasze dane do REST API - (o tym jak wygenerować clientID oraz clientSecret można przeczytać [tutaj](https://developer.allegro.pl/auth/#DeviceFlow)
1. Zmodyfikowany skrypt wysyłamy na własny serwer, bądź na nasz lokalny serwer z obsługą PHP
1. Tworzymy link do kanału RSS (więcej o tym niżej)
1. Sprawdzamy poprawność naszego linku w przeglądarce
1. Dodajemy link do naszego czytnika kanałów RSS

# Tworzenie linku do kanału RSS

Link do kanału RSS tworzymy poprzez podanie adresu serwera na jaki wysłaliśmy skrypt, a następnie podaniu ścieżki do skryptu. Przykładowo, gdy wysłaliśmy skrypt do głównego katalogu serwera, to podstawowym adresem kanału RSS będzie:
```
http://www.naszadomena.pl/index.php?
```
Do takiego adresu dodajemy kolejne parametry. Przed każdym kolejnym parametrem dodajemy znak `&`. Poniżej podałem kilka przykładowe linki do kanałów z wykorzystaniem parametrów.

## 1. Wymagane parametry
Użycie jednego z poniższych parametrów jest wymagane, żeby wyświetlić jakiekolwiek oferty. Oczywiście możemy wykorzystać też dwa z tych parametrów jednocześnie.

### Wyszukiwana fraza: `string`
W parametrze `string` określamy frazę, która będzie wyszukiwana w tytułach ofert. Jeśli składa się ona z kilku wyrazów, spacje zamieniamy na znak `+`. Przykładowo:
```
string=szukany+przedmiot
```

### ID kategorii: `categoryId`
W parametrze `categoryId` możemy podać ID kategorii, z której chcemy wyświetlać oferty. Przykładowo:
```
categoryId=348
```
Niestety aktualnie ID kategorii musimy wyszukać ręcznie wchodząc do wybranej kategorii, np. [Akcesoria GSM](https://allegro.pl/kategoria/akcesoria-gsm-348). Tam w pasku adresu, po nazwie kategorii mozemy znaleźć numer, który jest właśnie ID kategorii.

## 2. Opcjonalne parametry
### Wyłączenie słów z wyszukiwania: `exclude`
W tym parametrze możemy podać słowa, które nie mają znajdować się w wyświetlonych ofertach. Dzięki temu pozbędziemy się podobnych ofert do tej, której szukamy. Może nam to oszczędzić wiele czasu na sprawdzanie ofert, którymi na pewno nie jesteśmy zainteresowani. Kolejne słowa możemy podawać ze znakiem `+`, przykładowo:
```
exclude=ram+cpu+gpu
```
### Wyszukiwanie w opisach i parametrach ofert: `description`
Parametr `description` określa, czy oprócz szukania naszej frazy w tytułach, chcemy jej też szukać w opisach i parametrach ofert. Jeśli chcemy rozszerzyć wyszukiwanie, to dodajemy parametr:
```
description=1
```
### Wyszukiwanie ofert konkretnego sprzedawcy: `sellerId`
Parametr ten określa id sprzedawcy w którego przedmiotach chcemy wyszukiwać oferty. Wartość parametru można pobrać z linka karty ocen konkretnego sprzedawcy.
Będzie miał on formę https://allegro.pl/uzytkownik/ID_SPRZEDAWCY/oceny
```
description=1
```
### Wyszukiwanie w zakończonych ofertach: `closed`
```
closed=1
```
### Cena od: `priceFrom`
W parametrze `priceFrom` możemy określić minimalną cenę, od której powinny zaczynać się oferty. Możemy w nim podawać liczby całkowite, np. `1000`, jak i zmiennoprzecinkowe, np. `999.99`. Przykładowo:
```
priceFrom=150.50
```
### Cena do: `priceTo`
W parametrze `priceTo` możemy określić maksymalną cenę w jakiej wyświetlane będą oferty. Możemy w nim podawać liczby całkowite, np. `1000`, jak i zmiennoprzecinkowe, np. `999.99`. Przykładowo:
```
priceTo=300
```
### Typ ofert: `offerType`
W parametrze `offerType` możemy sprecyzować typy ofert, które będą dostarczane dla nas w kanale. Domyślnie wyświetlane są wszystkie typy ofert (ogłoszenia, licytacje i oferty kup teraz). Dodając ten parametr, możemy wyświetlić jedynie aukcje (`auction`) lub oferty kup teraz (`buyNow`). Przykładowo:
#### Kup Teraz 
```
offerType=1
```
#### Licytacja
```
offerType=2
```
#### Ogłoszenie
```
offerType=3
```
### Stan: `condition`
Work in progress...

### Parametry lokalizacyjne
Work in progress...

### Opcje oferty
Work in progress...

# Przykładowe wykorzystanie parametrów w kanałach RSS

- Najprostszy kanał RSS z wykorzystaniem jedynie parametru `string`
```
http://www.naszadomena.pl/alleRSS.php?string=szukany+przedmiot
```
- Kanał wykorzystujący większość dostepnych parametrów jednocześnie
```
http://www.naszadomena.pl/alleRSS.php?string=szukany+przedmiot&exclude=pomin+slowa&categoryId=348&priceTo=400
```


# Uwagi
- Nie jestem programistą PHP, dlatego skrypt może nie być napisany idealnie, niektóre założenia mogą być dyskusyjne dla profesjonalistów. Każdy zawsze może zrobić forka ;)
- Podobnie jak w WebAPI skrypt najpierw pobiera promowane oferty (w tytule pojawia się znacznik `(PROMOWANE)`). 
- Niestety, ale skrypt na ten moment nie jest tak dopracowany jak oryginalny [alleRSS](https://github.com/iskuzik/alleRSS). Tutaj brakuje sporej części opcji filtrowania. Jest to do odtworzenia, ale potrzebuję trochę czasu. Jakiekolwiek problemy/sugestie proszę zgłaszać przez Issues.
- Zarówno stare WebAPI jak i REST API nie zwracają informacji o czasie rozpoczęcia aukcji - czasami czytnik RSS zwraca zdublowane wpisy. Mam na to rozwiązanie, które będzie wymagało używania bazy danych MySQL.
- Pamiętajmy o limicie przydzielonym dla każdego klucza REST API: 9000 zapytań na minutę oraz limicie dla IP: 120 zapytań na sekundę.


# TODO
- [ ] dodanie większej ilości możliwych filtrów
- [ ] dodanie dat publikacji aukcji w oparciu o MySQL
- [ ] konwersja kanału z RSS 2.0 na Atom 


# LICENCJA

MIT

