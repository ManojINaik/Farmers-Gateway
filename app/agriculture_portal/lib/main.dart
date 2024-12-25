import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const AgriculturePortalApp());
}

class AgriculturePortalApp extends StatelessWidget {
  const AgriculturePortalApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Agriculture Portal',
      theme: ThemeData(
        primarySwatch: Colors.green,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: const AgriculturePortalHome(),
    );
  }
}

class AgriculturePortalHome extends StatefulWidget {
  const AgriculturePortalHome({super.key});

  @override
  State<AgriculturePortalHome> createState() => _AgriculturePortalHomeState();
}

class _AgriculturePortalHomeState extends State<AgriculturePortalHome> {
  late final WebViewController controller;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..enableZoom(true)
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            if (progress == 100) {
              setState(() {
                isLoading = false;
              });
            }
          },
          onPageStarted: (String url) {
            setState(() {
              isLoading = true;
            });
          },
          onPageFinished: (String url) async {
            // Enable WebGL and hardware acceleration
            await controller.runJavaScript('''
              if (!window.WebGLRenderingContext) {
                console.log('WebGL not supported');
              } else {
                const canvas = document.createElement('canvas');
                const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                if (!gl) {
                  console.log('WebGL not supported');
                }
              }
            ''');

            // Inject jQuery first
            await controller.runJavaScript('''
              if (typeof jQuery === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
                script.type = 'text/javascript';
                document.head.appendChild(script);
              }
            ''');
            
            // Clear memory
            await controller.clearCache();
            await controller.clearLocalStorage();
            
            setState(() {
              isLoading = false;
            });
          },
          onWebResourceError: (WebResourceError error) {
            setState(() {
              isLoading = false;
            });
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('Error: ${error.description}'),
                backgroundColor: Colors.red,
                duration: const Duration(seconds: 3),
              ),
            );
          },
        ),
      )
      ..setUserAgent('AgriculturePortal-Mobile')
      ..setBackgroundColor(Colors.transparent)
      ..loadRequest(Uri.parse('http://192.168.56.1/Agriculture-portal'));
  }

  @override
  void dispose() {
    controller.clearCache();
    controller.clearLocalStorage();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        if (await controller.canGoBack()) {
          await controller.goBack();
          return false;
        }
        return true;
      },
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Agriculture Portal'),
          actions: [
            IconButton(
              icon: const Icon(Icons.refresh),
              onPressed: () {
                controller.reload();
              },
            ),
          ],
        ),
        body: Stack(
          children: [
            WebViewWidget(controller: controller),
            if (isLoading)
              const Center(
                child: CircularProgressIndicator(
                  valueColor: AlwaysStoppedAnimation<Color>(Colors.green),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
