

## Eticaret Demo

Bu uygulama eticaret uygulamasının bazı modüllerini içermektedir.

## Kurulum

Env dosyasını kopyalayın.
```
cp .env.example .env
```

Env dosyasında gerekli değişikliklerinizi yapın

docker compose çalıştırın.
```
docker-compose up -d
```

## Dökümantasyon
https://ecommerce.vobiseller.com/docs

## Yapılan/Yapılacak İşler

- [x] Siparişler için, ekleme / silme / listeleme işlemlerinin gerçekleştirilebileceği bir RESTful API servisleri oluşturuldu.
- [x] Verilen siparişler için indirimleri hesaplayan küçük bir RESTful API servisi oluşturuldu.
- [x] Swagger ile dökümantasyon ve swagger arayüzü eklendi.
- [x] Proje alt yapısı docker ile çalışacak şekilde ayarlandı.
- [x] Proje için digitalocean'da sunucu açıldı. cloudflare yönlendirme yapıldı.
- [x] Proje için digitalocean'da container registry açıldı.
- [x] Github actions ile auto deployment süreçleri yapılandırıldı.
- [ ] Sipariş indirim uygulama testinin yazılması
- [ ] Sipariş ekleme/listeleme/silme için testlerin yazılması.

#### Sipariş Kuralları
- Yeni sipariş eklenirken, satın alınan ürünün stoğu yeterli değilse (**products.stock**) bir hata mesajı döndürün.
- Payload validasyonu.

#### İndirim Kuralları
- Toplam 1000TL ve üzerinde alışveriş yapan bir müşteri, siparişin tamamından %10 indirim kazanır.
- **2** ID'li kategoriye ait bir üründen 6 adet satın alındığında, bir tanesi ücretsiz olarak verilir.
- **1** ID'li kategoriden iki veya daha fazla ürün satın alındığında, en ucuz ürüne %20 indirim yapılır.

Gelecekte daha fazla indirim kuralı eklenebilecek şekilde geliştirildi.
