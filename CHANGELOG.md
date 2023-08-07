2.8.3	|	Release date: **07.08.2023**
============================================
* Bug-Fixes:
  - Fix GatewayConfigExtController when the Selected Factory is 'offline'


2.8.2	|	Release date: **03.08.2023**
============================================
* Bug-Fixes:
  - Fix Fixtures Services Configuration.


2.8.1	|	Release date: **03.08.2023**
============================================
* New Features:
  - Add a Doctrine Migration.
  - Create Fixtures For Paid Services and Pricing Plans.
* Bug-Fixes:
  - Fix CurrencyController .


2.8.0	|	Release date: **01.08.2023**
============================================
* New Features:
  - Create Pricing Plan Model.
  - add Pricing Plan Models as Sylius Resources.
  - Add Doctrine Migration for Pricing Plan Entities.
  - Create Controllers, Views and Forms for Pricing Plans Models.
  - Add to Simple Data Fixture.
  - Create a Repository Class for PricingPlanCategory Model.
  - Fix Pricing Plan Model.
  - Update PricingPlan Form and View.
  - Improve PricingPlanForm.
  - Prepair PricingPlanController .
  - Fix PricingPlan Doctrine Mapping.
  - Prepair PricingPlan Index Page.
  - Prepair PricingPlan Update Page.
  - Update Translations..


2.7.2	|	Release date: **26.07.2023**
============================================
* New Features:
  - Add a Doctrine Migration.


2.7.1	|	Release date: **15.07.2023**
============================================
* Bug-Fixes:
  - Fix Twig Templates That Using Macros Alerts.


2.7.0	|	Release date: **15.07.2023**
============================================
* New Features:
  - Update Composer Requirement Versions.


2.6.7	|	Release date: **28.03.2023**
============================================
* Bug-Fixes:
  - Fix GatewayConfigsExampleFactory
  - Fix Data Fixtures.
  - Fix Data Fixtures Services.
  - Fix Array Nodes Definitions in Fixtures.
  - Fix Sample Data Fixtures Configuration.
  - Fix Sample Data Fixtures.
  - Fix Products Fixture.


2.6.6	|	Release date: **27.03.2023**
============================================
* Bug-Fixes:
  - Fix SampleData Fixtures Config.


2.6.5	|	Release date: **27.03.2023**
============================================
* Bug-Fixes:
  - Fix SampleData Fixtures Config.


2.6.4	|	Release date: **27.03.2023**
============================================
* New Features:
  - Add Fixture Suit For Sample Data.


2.6.3	|	Release date: **24.03.2023**
============================================
* New Features and Improvements:
  - Improve Add To Cart Action.
  - Add TODO List In Documentation.
  - Improve ShoppingCartController .


2.6.2	|	Release date: **19.03.2023**
============================================
* New Features, Refactoring and Improvements:
  - Refactoring OrderItem Model.
  - Rename Trait to PaidServiceSubscriptionTrait .
  - Fix OrderItemInterface.
  - Add a DoctrineMigration
  - Add Currency Twig Extensions.
  - Change Namespace of Product Controllers.
  - Create ShoppingCartController
  - Add SessionId Into Order Model.
  - Add a Doctrine Migration.
  - Create OrderRepository.


2.6.1	|	Release date: **14.03.2023**
============================================
* New Features and Improvements:
  - Unification Price Orm Types.
  - Add a Doctrine Migration.
  - Add New Fields to Product Model.
  - Add New Doctrine Migration.
  - Add Product Pictures Fieldset.


2.6.0	|	Release date: **13.03.2023**
============================================
* New Features:
  - Improve GatewayConfig Index Page.
  - Add Currency Field into GatewayConfig.
  - Add Doctrine Migrations For Extended Project.
* Bug-Fixes:
  - Fix GatewayConfigExt Controller.
  - Fix PaymentMethodConfigExt Controller.


2.5.1	|	Release date: **13.03.2023**
============================================
* New Features:
  - Add Product  Resources
* Bug-Fixes and Improvements:
  - Fix All Forms That Extend AbstractForm.
  - Improw Gateway Config Index Page.
  - Move GatewayConfig Index Page to Resource Index.
  - Move PaymentMethodConfig Index Page to Resource Index.
  - Improve Currency Index Page.
  - Fix Duplicate Key in Translations.
  - Add Translations For Catalog Resources For Menu.
  - Fix Product Model.
  - Fix OrderItem Mapping.
  - Fix ProductCategoryController
  - Fix ProductCategory Edit Template.
  - Fix Products Index Template.
  - Not Display Uncategorized Products For Now.
  - Import Currency Class in ProductForm.
  - Extend Sylius Currency Interface.
  - Fix Products Update Template.
  - Make Category for Product Required.
  - Fix Products Update Template.


2.5.0	|	Release date: **09.03.2023**
============================================
* New Features:
  - Add Sylius Currency in Dependencies.
  - Add Currency and ExchangeRate Resources.
  - Add Currency Forms and Routes.
  - Add Menu Translations.
  - Add Views for Currency Resources.
  - Add Controllers for Currency Resources.
  - Make Currency Resource Forms to Extend Vankosoft Application AbstractForm.
  - Add Interfaces to Currency Resources Configuration.
  - Fix ExchangeRateForm Missing Constant.
  - Add Update Ttemplate for ExchangeRate Resource.
  - Fix Validation For Currency Resources.
  - Add Currency Validator Services.
  - Fix Currency Context Service.
  - Fix ExchangeRate Index Template.


2.4.1	|	Release date: **08.03.2023**
============================================
* New Features:
  - Add Requirement For Borica Bundle.


2.4.0	|	Release date: **08.03.2023**
============================================
* New Features:
  - Improve Gateway Config Form for Paysera Gateway.
  - Improve Gateway Config Options Form.
* Bug-Fixes:
  - Fix GatewayConfigExtController
  - Fix Gateway Config Options Template.
  - Fix Edit of Gateway Config.


2.3.3	|	Release date: **24.01.2023**
============================================
* Bug-Fixes:
  - Fix a Deprecation.
  - Fix Get Session for Symfony 6.
  - Fix Get User for Symfony 6.


2.3.2	|	Release date: **06.01.2023**
============================================
* Bug-Fixes:
  - Fix Templates Index Pages Table Heads For Bootstrap 5


2.3.1	|	Release date: **05.01.2023**
============================================
* Bug-Fixes:
  - Fix Main Menu Template Abour Bootstrap 5


2.3.0	|	Release date: **20.12.2022**
============================================
* New Features, Fixes and Improvements:
  - Add Link to Paysera Home
  - Fix Model PaymentMethod .
  - Update composer.json


2.2.2	|	Release date: **03.12.2022**
============================================
* New Features:
  - Add Paysera Gateway.
  - Add Paysera Checkout Controller.
  - Configure PayseraGatewayFactory .
  - Add Link to Paysera Manual


2.2.1	|	Release date: **01.12.2022**
============================================
* Bug-Fixes:
  - Fix Stupid Bug.


2.2.0	|	Release date: **01.12.2022**
============================================
* New Features:
  - Update Payum Packages.
  - Create PaypalRestController Checkout Controller.
  - Add Debug Functionality to AbstractCheckoutController .
  - Add a Borica Checkout Controller.
  - Add Borica Prepare Route.
  - Improve GatewayConfig Model.
* Bug-Fixes:
  - Fix Gateway Config Controller for paypal_rest Factory.


2.1.2	|	Release date: **24.09.2022**
============================================
* New Features:
  - Remove Calling of getDoctrine() method from All Controllers and Inject Doctrine in Constructors.


2.1.1	|	Release date: **06.09.2022**
============================================
* New Features:
  - AdminPanel Set Current Menu Item.


2.1.0	|	Release date: **29.08.2022**
============================================
* New Features:
  - Add VankoSoft Menu Translations.


2.0.2	|	Release date: **17.08.2022**
============================================
* Bug-Fixes:
  - Fix Config Extension.


2.0.1	|	Release date: **17.08.2022**
============================================
* New Features:
  - Begin Suport for PHP 8
* Bug-Fixes:
  - Fix Config Extension.


2.0.0	|	Release date: **12.06.2022**
============================================
* New Features:
  - Create Paid Subscription When Payment is Done Successfully.
  - Add Composer Package Requirement.


1.0.2	|	Release date: **24.05.2022**
============================================
* New Features:
  - Add Doctrine Mappings on Model Traits.


1.0.1	|	Release date: **24.05.2022**
============================================
* Bug-Fixes:
  - Fix Order and OrderItem Doctrine Mappings.


1.0.0	|	Release date: **24.05.2022**
============================================
* New Features:
  - Add Description Field in Order and Used it in Payment.
  - StripeCheckoutController is Done!
  - PaypalExpressCheckout Controller is Done!.
  - Add Order Statuses For Success and Failed Paiments.
* Bug-Fixes, Improvements and Refactoring:
  - Fix StripeCheckout Rotes!
  - Payment Checkout Refactoring.
  - Improve AbstractCheckoutController::doneAction
  - Refactoring
  - Fix Payment Methods Form
  - Stripe Amount Amount must convert to at least 100


0.2.0	|	Release date: **23.05.2022**
============================================
* Bug-Fixes , Improvements, Refactoring:
  - Update composer.json
  - Update VisualParadigm Diagrams.
  - Improve PaymentMethod Model.
  - Prepair PaymentMethod Resource.
  - Fix Route in Template.
  - Improve Payment Method Config Template.
  - Payum Prepend Extension is DONE!
  - Add README file.
  - Add Checkout Test Controllers.
  - Update CreditCardForm.
  - Improve AbstractCheckoutController and CreditCardForm Template.
  - Checkout Done Template.
  - Checkout Done Template.
  - Detach this bundle from UsersSubscriptionsBundle and Full Refactoring and Add Orders Model.
  - Fix GatewayConfig Doctrine Mapping.
  - Fix Model Method.
  - Fix GatewayConfigForm and PaymentMethodForm.
  - Improve PaymentMethodConfig Index Template.
  - Improve GatewayConfig  Index Template.
  - Fix Model Order.
  - Fix AbstractCheckoutController.
  - Add Payment Route into PaymentMethod Model.
  - Add Payum Tokens as Doctrine Orm.
  - Add Order Statuses and Make Order and Payment Timestampable.
  - Add PaymentController with AddToCard, showPaymentMethods and showCreditCard Actions.
  - Add CheckoutControllers Services.
  - Fix Checkout\PaymentController.
  - Add Method getShoppingCard() into the AbstractCheckoutController.
  - Order Model Set Default Values.
  - Fix PaymentController::showCreditCardForm
  - Add Publishable Key in Stripe Credit Card Form.
  - Improve CreditCardForm Template


0.1.0	|	Release date: **17.05.2022**
============================================
* New Features and Improvements:
  - Again Add PaymentDetails Model.
  - Add a New CreditCardForm and Controller that Show It.
  - Controllers Namespace Refactoring.
  - CreditCard Form Template Add Variables for Form Action and Method.
  - Improve Payment Model.
  - Remove Model PaymentDetails.
  - Create GatewayConfig Factories Configurable From Parameters.
  - Improve GatewayConfigForm
  - Translate GatewayConfigForm Fields Labels.
  - Add Payment Methods routes
  - Gateway Config Options Json Response.


0.0.2	|	Release date: **12.05.2022**
============================================
* Bug-Fixes, Refactoring and Improvements:
  - Add Default ORM Driver in Configuration.
  - Big Refactoring.
  - Fix Routes.
  - Refactoring and Fixing Templates and Payment Configs AdminPanel Menu.
  - Fixes for Gateway Config Form Template.
  - Fixes for Gateway Config Form Template.
  - Add All Translations for Gateway Config Form and Templates.
  - Add All Translations for Gateway Config Form and Templates.
  - Fix GatewayConfig Options Form.
  - Add Defaults For Route Parameter to Can Be Added in Menu.
  - Fix GatewayConfigExtController.
  - Prepare Payment Routes.
  - Improve composer.json


0.0.1	|	Release date: **12.03.2022**
============================================
* New Features:
  - Initial commit
  - First Commit (Original Bundle)
  - Big Refactoring.


