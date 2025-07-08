@extends('errors::layout')

@section('title', __('Error 403 - Acceso Denegado'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'No tienes Permisos para Acceder a Esta Página'))
