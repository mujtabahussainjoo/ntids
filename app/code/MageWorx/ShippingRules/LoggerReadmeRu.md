# MageWorx Shipping Rules Logger for Magento 2

## Using of the logger

### Notes

1. Наиболее целесообразно использовать логгер на странице корзины и чекаута, где изменение стоимости доставки
расчитывается наиболее часто. На регулярных страницах, вроде каталог продукт, вы можете не увидеть изменений.

2. Логгер инициализируется через requireJS, по этой причине он может быть недоступен в первые секунды загрузки страницы.
Загружается логер с адреса:

        pub/static/frontend/{theme_vendor}/{theme}/{locale}/MageWorx_ShippingRules/js/shippingRulesLogger.js

3. Доступ к логеру осуществляется из консоли браузера следующим кодом

        require('uiRegistry').get('shippingRulesLogger')

### Entities

1. Story - массив, итерации расчетов на стороне сервера в порядке их выполнения, хранит внутри себя Info
2. Info - объект, одна итерация расчетов на стороне сервера, содержит в себе именнованные методы (шипинг методы)
3. Method - объект, ключем которого является код метода вида carrier_method, содержит в себе свойство rules 
со списком всех рулов которые могли быть применены к данному методу (валидные по стору и кастомер группе рулы). Если
искомого рула нет в списке для метода, то следует в первую очередь проверить в руле включен ли он для текущего стора и
находится ли текущий кастомер в списке валидных кастомер групп для данного рула.
4. Rule - объект, ключем которого является Rule Id. Содержит информацию о ходе применения рула. Возможные свойства и их
значения:

    - **index** *(int)* - порядок применения рула. Первый примененный рул имеет индекс 0.
    - **rule_id** *(string|int)* - ид соответствующего рула, совпадает с ключом
    - **sort_order** *(string|int)* - приоритет рула указанный в его настройках
    - **valid** *(boolean)* - прошла ли валидация. Если значение false рул не был применен. Следует смотреть кондишены.
    - **processed** *(boolean)* - удалось ли рулу осуществить хоть какое-либо действие с этим шипинг методом (отключить, изменить стоимость)
    - **disabled** *(boolean)* - отключен ли данный метод этим рулом
    - **cost_overwrote** *(boolean)* - была ли переписана стоимость текущего метода данным рулом
    - **invalid_shipping_method** *(boolean)* - текущий метод (внутри которого содержится данный рул) не находится в списке к применению (см. секцию Actions в форме рула)
    - **input_data** *(object)* - данные метода на входе (до применения текущего рула, совпадают с output_data предыдущего рула)
        + **availability** *(boolean)* - доступность метода (отключен = false)
        + **price** *(decimal)* - стоимость метода
    - **output_data** *(object)* - данные метода на выходе (после применения текущего рула, совпадают с input_data следующего рула)
        + **availability** *(boolean)* - доступность метода (отключен = false)
        + **price** *(decimal)* - стоимость метода
        + **detailed_actions** *(object)* - список экшенов примененных к данному рулу с деталями

### Methods

1. Получение всей истории:

        require('uiRegistry').get('shippingRulesLogger').getStory() 
        
2. Получение последней итерации расчетов:

        require('uiRegistry').get('shippingRulesLogger').getInfo()
        
3. Получение определенной итерации расчетов (пример для первой итерации и третьей итерации):

        require('uiRegistry').get('shippingRulesLogger').getInfo(0)
        require('uiRegistry').get('shippingRulesLogger').getInfo(2)

4. Принудительно обновление истории (запрос на сервер):

        require('uiRegistry').get('shippingRulesLogger').refreshStory()
        
5. Узнать в каком руле впервые был отключен метод по коду (на примере метода flatrate и length19testcarrier_firstclassdelivery):

        require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('flatrate_flatrate')
        require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('length19testcarrier_firstclassdelivery')
        
    где:
    
    + flatrate и length19testcarrier - коды керриера
    + flatrate и firstclassdelivery - коды метода
    
    Если требуется получить данную информацию для определенного Info, следует передать его id вторым параметром:
    
        require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('flatrate_flatrate', 1)
        require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('length19testcarrier_firstclassdelivery', 5)
        
    где 1 и 5 это ключи (id) Info, которые можно получить используя метод `getStory()`
    
6. Получить информацию по конкретному методу (по умолчанию для последнего Info, если id Info явно не указано вторым параметром):

        require('uiRegistry').get('shippingRulesLogger').getMethodInfoByCode('length19testcarrier_firstclassdelivery')
        require('uiRegistry').get('shippingRulesLogger').getMethodInfoByCode('flatrate_flatrate', 1)
        
7. Посмотреть разницу для конкретного метода по указанным рулам:

        require('uiRegistry').get('shippingRulesLogger').showDiff('flatrate_flatrate', 21, 24)
        
    где 21 и 24 это id рулов. Так же можно указать 4м параметром id Info чтобы посмотреть разницу в контексте конкретного Info
    (по умолчанию используется последнее Info)
    
        require('uiRegistry').get('shippingRulesLogger').showDiff('flatrate_flatrate', 21, 24, 4)
        
    где 21 и 24 это id рулов, а 4 - id Info.