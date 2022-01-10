export default class RobokassaWidget {
    constructor(serviceApi) {
        console.info('Robokassa widget init')
        console.log(serviceApi.getToken());
        // init API here for getting payment info by token.
        // GET token from API
    }

    init() {
        Robokassa.StartPayment({
            MerchantLogin: 'demo',
            OutSum: '11.00',
            Description: 'Оплата заказа в Тестовом магазине ROBOKASSA',
            shp_Item: '1',
            Culture: 'ru',
            Encoding: 'utf-8',
            SignatureValue: '3925b771e47d405cbcbb492daa936824'
        });
    }

    close() {
        Robokassa.ClosePaymentForm();
    }
}