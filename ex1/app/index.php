<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>sandbox</title>
    <script src="//api.bitrix24.com/api/v1/"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
      integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <style>
      div {
        margin-top: .5rem;
        margin-bottom: .5rem;
      }

      input {
        margin-left: .5rem;
        margin-right: .5rem;
      }

      button {
        margin-left: .5rem;
        margin-right: .5rem;
      }
    </style>
  </head>

  <body>
    <section id="leads">
      <h1>Лиды</h1>
      <div class="test-wrapper">
        <button id="lead_fields">Поля лида</button>
      </div>
      <div class="create-wrapper">
        <button id="create_lead">Добавить лид</button>
      </div>
      <div class="get-wrapper">
        <input type="number" class="get-id"><button id="get_lead">Получить лид</button>
      </div>
      <div class="delete-wrapper">
        <input type="number" class="delete-id"><button id="delete_lead">Удалить лид</button>
      </div>
    </section>
    <section id="company">
      <h1>Компании</h1>
      <div class="test-wrapper">
        <button id="company_fields">Поля компании</button>
      </div>
      <div class="create-wrapper">
        <button id="create_company">Добавить компанию</button>
      </div>
      <div class="get-wrapper">
        <input type="number" class="get-id"><button id="get_company">Получить компанию</button>
      </div>
      <div class="delete-wrapper">
        <input type="number" class="delete-id"><button id="delete_company">Удалить компанию</button>
      </div>
    </section>
    <section id="contact">
      <h1>Контакты</h1>
      <div class="test-wrapper">
        <button id="contact_fields">Поля контакта</button>
      </div>
      <div class="create-wrapper">
        <button id="create_contact">Добавить контакт</button>
      </div>
      <div class="get-wrapper">
        <input type="number" class="get-id"><button id="get_contact">Получить контакт</button>
      </div>
      <div class="delete-wrapper">
        <input type="number" class="delete-id"><button id="delete_contact">Удалить контакт</button>
      </div>
    </section>
    <section id="deal">
      <h1>Сделки</h1>
      <div class="test-wrapper">
        <button id="deal_fields">Поля сделки</button>
      </div>
      <div class="create-wrapper">
        <button id="create_deal">Добавить сделку</button>
      </div>
      <div class="get-wrapper">
        <input type="number" class="get-id"><button id="get_deal">Получить сделку</button>
      </div>
      <div class="delete-wrapper">
        <input type="number" class="delete-id"><button id="delete_deal">Удалить сделку</button>
      </div>
    </section>
    <section id="invoice">
      <h1>Счета</h1>
      <div class="test-wrapper">
        <button id="invoice_fields">Поля счета</button>
      </div>
      <div class="create-wrapper">
        <button id="create_invoice">Добавить счет</button>
      </div>
      <div class="get-wrapper">
        <input type="number" class="get-id"><button id="get_invoice">Получить счет</button>
      </div>
      <div class="delete-wrapper">
        <input type="number" class="delete-id"><button id="delete_invoice">Удалить счет</button>
      </div>
    </section>
    <script>
      const user_id = 92;

      $(document).ready(function () {
        /* 
        * Лиды
        */
        $('#lead_fields').click(function () {
          BX24.callMethod(
            'crm.lead.fields',
            {},
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#create_lead').click(function () {
          BX24.callMethod(
            'crm.lead.add',
            {
              fields:
              {
                'TITLE': 'Test',
                'NAME': 'Test',
                'SECOND_NAME': 'Test',
                'LAST_NAME': 'Test',
                'OPENED': 'Y',
                'ASSIGNED_BY_ID': user_id,
              },
              params: { 'REGISTER_SONET_EVENT': 'N' }
            },
            function (result) {
              if (result.error())
                console.error(result.error());
              else {
                console.info('Создан лид с ID ' + result.data());
                $('#leads .get-id').val(result.data());
                $('#leads .delete-id').val(result.data());
              }
            }
          );
        });
        $('#get_lead').click(function () {
          const id = $('#leads .get-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            'crm.lead.get',
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#delete_lead').click(function () {
          const id = $('#leads .delete-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            "crm.lead.delete",
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.info(result.data());
            }
          );
        });
        /* 
        * Компании
        */
        $('#company_fields').click(function () {
          BX24.callMethod(
            'crm.company.fields',
            {},
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#create_company').click(function () {
          BX24.callMethod(
            'crm.company.add',
            {
              fields:
              {
                'TITLE': 'Test',
                'ASSIGNED_BY_ID': user_id,
              },
              params: { 'REGISTER_SONET_EVENT': 'N' }
            },
            function (result) {
              if (result.error())
                console.error(result.error());
              else {
                console.info('Создана компания с ID ' + result.data());
                $('#company .get-id').val(result.data());
                $('#company .delete-id').val(result.data());
              }
            }
          );
        });
        $('#get_company').click(function () {
          const id = $('#company .get-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            'crm.company.get',
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#delete_company').click(function () {
          const id = $('#company .delete-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            "crm.company.delete",
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.info(result.data());
            }
          );
        });
        /* 
        * Контакты
        */
        $('#contact_fields').click(function () {
          BX24.callMethod(
            'crm.contact.fields',
            {},
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#create_contact').click(function () {
          BX24.callMethod(
            'crm.contact.add',
            {
              fields:
              {
                'NAME': 'Test',
                'LAST_NAME': 'Test',
                'SECOND_NAME': 'Test',
                'ASSIGNED_BY_ID': user_id,
              },
              params: { 'REGISTER_SONET_EVENT': 'N' }
            },
            function (result) {
              if (result.error())
                console.error(result.error());
              else {
                console.info('Создан контакт с ID ' + result.data());
                $('#contact .get-id').val(result.data());
                $('#contact .delete-id').val(result.data());
              }
            }
          );
        });
        $('#get_contact').click(function () {
          const id = $('#contact .get-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            'crm.contact.get',
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#delete_contact').click(function () {
          const id = $('#contact .delete-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            "crm.contact.delete",
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.info(result.data());
            }
          );
        });
        /* 
        * Сделки
        */
        $('#deal_fields').click(function () {
          BX24.callMethod(
            'crm.deal.fields',
            {},
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#create_deal').click(function () {
          BX24.callMethod(
            'crm.deal.add',
            {
              fields:
              {
                'TITLE': 'Test',
                'ASSIGNED_BY_ID': user_id,
              },
              params: { 'REGISTER_SONET_EVENT': 'N' }
            },
            function (result) {
              if (result.error())
                console.error(result.error());
              else {
                console.info('Создана сделка с ID ' + result.data());
                $('#deal .get-id').val(result.data());
                $('#deal .delete-id').val(result.data());
              }
            }
          );
        });
        $('#get_deal').click(function () {
          const id = $('#deal .get-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            'crm.deal.get',
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#delete_deal').click(function () {
          const id = $('#deal .delete-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            "crm.deal.delete",
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.info(result.data());
            }
          );
        });
        /* 
        * Счета
        */
        $('#invoice_fields').click(function () {
          BX24.callMethod(
            'crm.invoice.fields',
            {},
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#create_invoice').click(function () {
          var current = new Date();
          var nextMonth = new Date();
          nextMonth.setMonth(current.getMonth() + 1);
          const date2str = function (d) {
            return d.getFullYear() + '-' + paddatepart(1 + d.getMonth()) + '-' + paddatepart(d.getDate()) + 'T' + paddatepart(d.getHours())
              + ':' + paddatepart(d.getMinutes()) + ':' + paddatepart(d.getSeconds()) + '+03:00';
          };
          const paddatepart = function (part) {
            return part >= 10 ? part.toString() : '0' + part.toString();
          };
          BX24.callMethod(
            'crm.invoice.add',
            {
              fields:
              {
                'STATUS_ID': 'P',
                'ORDER_TOPIC': 'Test123',
                'ACCOUNT_NUMBER': 'Test',
                'DATE_BILL': date2str(current),
                'DATE_PAY_BEFORE': date2str(nextMonth),
                'PAY_SYSTEM_ID': 8,
                'PERSON_TYPE_ID': 1,
                'RESPONSIBLE_ID': user_id,
                'UF_CONTACT_ID': user_id,
                'PRODUCT_ROWS': [
                  { 'ID': 0, 'PRODUCT_ID': 438, 'PRODUCT_NAME': 'Товар 01', 'QUANTITY': 1, 'PRICE': 100 },
                  { 'ID': 0, 'PRODUCT_ID': 515, 'PRODUCT_NAME': 'Товар 77', 'QUANTITY': 1, 'PRICE': 118 }
                ]
              },
              params: { 'REGISTER_SONET_EVENT': 'N' }
            },
            function (result) {
              if (result.error())
                console.error(result.error());
              else {
                console.info('Создан счет с ID ' + result.data());
                $('#invoice .get-id').val(result.data());
                $('#invoice .delete-id').val(result.data());
              }
            }
          );
        });
        $('#get_invoice').click(function () {
          const id = $('#invoice .get-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            'crm.invoice.get',
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.dir(result.data());
            }
          );
        });
        $('#delete_invoice').click(function () {
          const id = $('#invoice .delete-id').val();
          if (!id) return console.error('Input must be not empty!');
          BX24.callMethod(
            "crm.invoice.delete",
            { id: id },
            function (result) {
              if (result.error())
                console.error(result.error());
              else
                console.info(result.data());
            }
          );
        });
      });

      BX24.init(app);
      function app() {
        const initDate = BX24.getAuth();
        console.log('ititDate: ', initDate);
      }
      BX24.fitWindow();

    </script>

  </body>

</html>
