module Main exposing (main)

import Browser
import Html exposing (Html, div, h1, text, input, button, select, option)
import Html.Attributes exposing (value, type_, placeholder, disabled, autofocus)
import Html.Events exposing (onClick, onInput)
import Http
import Json.Decode as Decode
import Json.Encode as Encode
import Regex
-- import Task exposing (Task)  -- Removed unused import

-- CONFIG
apiBaseUrl : String
apiBaseUrl = "http://localhost:8000"

-- MODEL

type alias Country =
    { name : String
    , code : String
    , flag : String
    , validatePhone : String -> Bool
    }

type alias Model =
    { countryCode : String
    , countries : List Country
    , selectedCountry : Maybe Country
    , phone : String
    , fullPhone : String
    , otp : String
    , message : String
    , otpSent : Bool
    , otpEnabled : Bool
    }

init : () -> ( Model, Cmd Msg )
init _ =
    ( { countryCode = "+503"
      , countries = initialCountries
      , selectedCountry = List.head initialCountries
      , phone = ""
      , fullPhone = ""
      , otp = ""
      , message = ""
      , otpSent = False
      , otpEnabled = False
      }
    , Cmd.none
    )

initialCountries : List Country
initialCountries =
    [ Country "El Salvador" "+503" "🇸🇻" validateElSalvadorPhone
    , Country "United States" "+1" "🇺🇸" validateUSPhone
    -- Add more countries with their specific validation functions
    ]

validateElSalvadorPhone : String -> Bool
validateElSalvadorPhone phone =
    let
        digitsOnly =
            String.filter Char.isDigit phone
    in
    String.length digitsOnly == 8

-- Function to validate US phone numbers
validateUSPhone : String -> Bool
validateUSPhone phone =
    let
        -- Regular expression for validating US phone numbers
        usPhoneRegex = Regex.fromString "^(\\+1)?\\s*\\(?\\d{3}\\)?[-.\\s]?\\d{3}[-.\\s]?\\d{4}$"
    in
    case usPhoneRegex of
        Just regex ->
            Regex.contains regex phone

        Nothing ->
            False

-- UPDATE

type Msg
    = UpdatePhone String
    | SelectCountry Country
    | SendOTP
    | OTPSent (Result Http.Error Response)
    | UpdateOTP String
    | VerifyOTP
    | OTPVerified (Result Http.Error Response)
    | UpdateCountryCode String  -- New message for country code update

type alias Response =
    { message : String
    }

responseDecoder : Decode.Decoder Response
responseDecoder =
    Decode.map Response
        (Decode.field "message" Decode.string)

update : Msg -> Model -> (Model, Cmd Msg)
update msg model =
    case msg of
        SelectCountry country ->
            ( { model | selectedCountry = Just country, countryCode = country.code }, Cmd.none )  -- Update countryCode when a country is selected

        UpdatePhone newPhone ->
            let
                fullPhone =
                    case model.selectedCountry of
                        Just country ->
                            country.code ++ newPhone

                        Nothing ->
                            newPhone
            in
            ( { model | phone = newPhone, fullPhone = fullPhone }, Cmd.none )

        SendOTP ->
            if model.countryCode == "" then
                (model, Cmd.none)  -- Return the model without sending OTP
            else
                (model, sendOTPRequest model.phone model.countryCode)  -- Use model.countryCode to access the country code

        OTPSent result ->
            case result of
                Ok res ->
                    ( { model | message = res.message, otpEnabled = True, otpSent = True }, Cmd.none )

                Err error ->
                    let
                        errorMessage =
                            case error of
                                Http.BadUrl url ->
                                    "Bad URL: " ++ url
                                Http.Timeout ->
                                    "Request timed out"
                                Http.NetworkError ->
                                    "Network error"
                                Http.BadStatus 511 ->
                                    "Network authentication required. Please check your connection."
                                Http.BadStatus statusCode ->
                                    "Bad status: " ++ String.fromInt statusCode
                                Http.BadBody message ->
                                    "Bad body: " ++ message
                    in
                    ( { model | message = "Failed to send OTP: " ++ errorMessage, otpEnabled = False }, Cmd.none )

        UpdateOTP newOTP ->
            ( { model | otp = newOTP }, Cmd.none )
        VerifyOTP ->
            let
                -- fullPhone = model.countryCode ++ model.phone  -- Concatenate country code and phone number
                verificationCmd =
                    performOTPVerification model.countryCode model.phone { otp = model.otp }
            in
            (model, verificationCmd)
        OTPVerified result ->
            case result of
                Ok res ->
                    ( { model | message = res.message }, Cmd.none )

                Err _ ->
                    ( { model | message = "Invalid OTP" }, Cmd.none )

        UpdateCountryCode code ->
            let
                updatedModel = { model | countryCode = code }
            in
            if String.isEmpty code then
                ( model, Cmd.none )  -- Return the current model if the code is empty
            else
                ( updatedModel, Cmd.none )  -- Update the model with the selected country code

        -- ... other cases remain the same

isValidPhone : Maybe Country -> String -> Bool
isValidPhone maybeCountry phone =
    case maybeCountry of
        Just country ->
            country.validatePhone phone

        Nothing ->
            False

-- VIEW

view : Model -> Html Msg
view model =
    div []
        [ h1 [] [ text "Phone Authentication" ]
        , viewCountrySelector model
        , viewPhoneInput model
        , if model.otpEnabled then
            viewOTPInput model
          else
            text ""
        , div [] [ text model.message ]
        ]

viewCountrySelector : Model -> Html Msg
viewCountrySelector model =
    div []
        [ select [ onInput (\code -> SelectCountry (findCountry code model.countries)) ]
            (List.map (\country ->
                option [ value country.code ]
                    [ text (country.flag ++ " " ++ country.name ++ " (" ++ country.code ++ ")")
                    ]
            ) model.countries)
        ]

viewPhoneInput : Model -> Html Msg
viewPhoneInput model =
    div []
        [ input
            [ type_ "tel"
            , placeholder "Phone number"
            , value model.phone
            , onInput UpdatePhone
            , autofocus True -- This should set focus on load
            ]
            []
        , button
            [ onClick SendOTP
            , disabled (not (isValidPhone model.selectedCountry model.phone))
            ]
            [ text "Send SMS" ]
        ]

viewOTPInput : Model -> Html Msg
viewOTPInput model =
    div []
        [ input
            [ type_ "text"
            , placeholder "Enter OTP"
            , value model.otp
            , onInput UpdateOTP
            , autofocus (model.otpSent) -- Set autofocus based on otpSent
            ]
            []
        , button [ onClick VerifyOTP ] [ text "Verify OTP" ]
        ]

findCountry : String -> List Country -> Country
findCountry code countries =
    countries
        |> List.filter (\c -> c.code == code)
        |> List.head
        |> Maybe.withDefault (Country "Unknown" "" "" (always False))  -- Added a default validation function

-- MAIN

main : Program () Model Msg
main =
    Browser.element
        { init = init
        , update = update
        , view = view
        , subscriptions = \_ -> Sub.none
        }


-- Functions for sending OTP and verifying OTP

sendOTPRequest : String -> String -> Cmd Msg
sendOTPRequest phone countryCode =
    if String.isEmpty countryCode then
        Cmd.none
    else
        Http.post
            { url = apiBaseUrl ++ "/auth/sms"
            , body = Http.jsonBody (Encode.object 
                [ ("phone", Encode.string phone)
                , ("country_code", Encode.string countryCode)
                ])
            , expect = Http.expectJson OTPSent responseDecoder
            }

performOTPVerification : String -> String -> ({ a | otp : String }) -> Cmd Msg
performOTPVerification countryCode phone otp =
    let
        fullPhone = countryCode ++ phone
    in
    Http.post
        { url = apiBaseUrl ++ "/auth/verify"
        , body = Http.jsonBody (Encode.object [ ("phone", Encode.string fullPhone), ("otp", Encode.string otp.otp) ])
        , expect = Http.expectJson OTPVerified responseDecoder
        }
